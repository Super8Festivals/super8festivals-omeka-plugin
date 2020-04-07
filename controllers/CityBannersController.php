<?php

class SuperEightFestivals_CityBannersController extends Omeka_Controller_AbstractActionController
{
    public function init()
    {
        // Set the model class so this controller can perform some functions,
        // such as $this->findById()
        $this->_helper->db->setDefaultModelName('SuperEightFestivalsCityBanner');
    }

    public function indexAction()
    {
        $request = $this->getRequest();

        $countryName = $request->getParam('countryName');
        $country = get_country_by_name($countryName);
        $this->view->country = $country;

        $cityName = $request->getParam('cityName');
        $city = get_city_by_name($country->id, $cityName);
        $this->view->city = $city;

        $this->redirect("/super-eight-festivals/countries/" . urlencode($country->name) . "/cities/" . urlencode($city->name));
        return;
    }

    public function addAction()
    {
        $request = $this->getRequest();

        $countryName = $request->getParam('countryName');
        $country = get_country_by_name($countryName);
        $this->view->country = $country;

        $cityName = $request->getParam('cityName');
        $city = get_city_by_name($country->id, $cityName);
        $this->view->city = $city;

        // Create new banner
        $banner = new SuperEightFestivalsCityBanner();
        $banner->country_id = $country->id;
        $banner->city_id = $city->id;
        $form = $this->_getForm($banner);
        $this->view->form = $form;
        $this->_processForm($banner, $form, 'add');
    }

    public function editAction()
    {
        $request = $this->getRequest();

        $countryName = $request->getParam('countryName');
        $country = get_country_by_name($countryName);
        $this->view->country = $country;

        $cityName = $request->getParam('cityName');
        $city = get_city_by_name($country->id, $cityName);
        $this->view->city = $city;


        $bannerID = $request->getParam('bannerID');
        $banner = get_city_banner_by_id($bannerID);
        $this->view->banner = $banner;

        $form = $this->_getForm($banner);
        $this->view->form = $form;
        $this->_processForm($banner, $form, 'edit');
    }


    public function deleteAction()
    {
        $request = $this->getRequest();

        $countryName = $request->getParam('countryName');
        $country = get_country_by_name($countryName);
        $this->view->country = $country;

        $cityName = $request->getParam('cityName');
        $city = get_city_by_name($country->id, $cityName);
        $this->view->city = $city;

        $bannerID = $request->getParam('bannerID');
        $banner = get_city_banner_by_id($bannerID);
        $this->view->banner = $banner;

        $form = $this->_getDeleteForm();
        $this->view->form = $form;
        $this->_processForm($banner, $form, 'delete');
    }

    protected function _getForm(SuperEightFestivalsCityBanner $banner = null)
    {
        $formOptions = array(
            'type' => 'super_eight_festivals_city_banner'
        );

        $form = new Omeka_Form_Admin($formOptions);

        $form->addElementToEditGroup(
            'select', 'city_id',
            array(
                'id' => 'city_id',
                'label' => 'City',
                'description' => "The city which the banner belongs to (required)",
                'multiOptions' => get_parent_city_options(),
                'value' => $banner->city_id,
                'required' => true
            )
        );

        $form->addElementToEditGroup(
            'file', 'file',
            array(
                'id' => 'file',
                'label' => 'File',
                'description' => "The banner image file",
                'required' => $banner->file_name == "" || !file_exists($banner->get_path()),
            )
        );

        $form->addElementToEditGroup(
            'checkbox', 'active',
            array(
                'id' => 'active',
//                    'disabled' => get_active_city_banner($banner->get_country()->id) == null,
                'label' => 'Active',
                'description' => "Make this the active banner?",
                'value' => $banner->active,
            )
        );

        return $form;
    }


    private function _processForm(SuperEightFestivalsCityBanner $banner, Zend_Form $form, $action)
    {
        $this->view->banner = $banner;

        if ($this->getRequest()->isPost()) {
            try {
                if (!$form->isValid($_POST)) {
                    $this->_helper->flashMessenger('There was an error on the form. Please try again.', 'error');
                    return;
                }
                try {
                    // delete
                    if ($action == 'delete') {
                        $banner->delete();
                        $this->_helper->flashMessenger("The banner for " . $banner->get_city()->name . " has been deleted.", 'success');
                    } //add
                    else if ($action == 'add') {
                        $banner->setPostData($_POST);
                        if ($banner->save()) {
                            $this->_helper->flashMessenger("The banner for " . $banner->get_city()->name . " has been added.", 'success');

                            // do file upload
                            $this->upload_file($banner);
                        }
                    } //edit
                    else if ($action == 'edit') {
                        // get the original so that we can use old information which doesn't persist well (e.g. files)
                        $originalRecord = get_city_banner_by_id($banner->id);
                        // set the data of the record according to what was submitted in the form
                        $banner->setPostData($_POST);
                        // if there is no pending upload, use the old files
                        if (!has_temporary_file('file')) {
                            $banner->file_name = $originalRecord->file_name;
                            $banner->thumbnail_file_name = $originalRecord->thumbnail_file_name;
                        }
                        if ($banner->save()) {
                            // display result dialog
                            $this->_helper->flashMessenger("The banner for " . $banner->get_city()->name . " has been edited.", 'success');

                            // only change files if there is a file waiting
                            if (has_temporary_file('file')) {
                                // delete old files
                                $originalRecord->delete_files();
                                // do file upload
                                $this->upload_file($banner);
                            }
                        }
                    }

                    // bring us back to the city page
                    $this->redirect("/super-eight-festivals/countries/" . urlencode($banner->get_country()->name) . "/cities/" . urlencode($banner->get_city()->name));
                } catch (Omeka_Validate_Exception $e) {
                    $this->_helper->flashMessenger($e);
                } catch (Omeka_Record_Exception $e) {
                    $this->_helper->flashMessenger($e);
                }
            } catch (Zend_Form_Exception $e) {
                $this->_helper->flashMessenger($e);
            }
        }
    }

    private function upload_file(SuperEightFestivalsCityBanner $city_banner)
    {
        list($original_name, $temporary_name, $extension) = get_temporary_file("file");
        $newFileName = uniqid($city_banner->get_internal_prefix() . "_") . "." . $extension;
        move_to_dir($temporary_name, $newFileName, $city_banner->get_city()->get_dir());
        $city_banner->file_name = $newFileName;
        $city_banner->save();
    }

}