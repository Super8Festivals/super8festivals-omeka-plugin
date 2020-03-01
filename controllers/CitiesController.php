<?php

class SuperEightFestivals_CitiesController extends Omeka_Controller_AbstractActionController
{

    public function init()
    {
        // Set the model class so this controller can perform some functions,
        // such as $this->findById()
        $this->_helper->db->setDefaultModelName('SuperEightFestivalsCity');
    }

    public function indexAction()
    {
        $request = $this->getRequest();

        $countryName = $request->getParam('countryName');
        $country = get_country_by_name($countryName);
        $this->view->country = $country;

        return;
    }

    public function singleAction()
    {
        $request = $this->getRequest();

        $countryName = $request->getParam('countryName');
        $country = get_country_by_name($countryName);
        $this->view->country = $country;

        $cityName = $request->getParam('cityName');
        $city = get_city_by_name($country->id, $cityName);
        $this->view->city = $city;

        return;
    }

    public function addAction()
    {
        // Create new city
        $city = new SuperEightFestivalsCity();
        $form = $this->_getForm($city);
        $this->view->form = $form;
        $this->_processForm($city, $form, 'add');
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

        $form = $this->_getForm($city);
        $this->view->form = $form;
        $this->_processForm($city, $form, 'edit');
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

        $form = $this->_getDeleteForm();
        $this->view->form = $form;
        $this->_processForm($country, $form, 'delete');
    }

    protected function _getForm(SuperEightFestivalsCity $city = null): Omeka_Form_Admin
    {
        $formOptions = array(
            'type' => 'super_eight_festivals_city'
        );

        $form = new Omeka_Form_Admin($formOptions);

        $form->addElementToEditGroup(
            'select', 'country_id',
            array(
                'id' => 'country_id',
                'label' => 'Country ID',
                'description' => "The ID of the country (required)",
                'multiOptions' => get_parent_country_options(),
                'value' => $city->country_id,
                'required' => true
            )
        );

        $form->addElementToEditGroup(
            'text', 'name',
            array(
                'id' => 'name',
                'label' => 'City Name',
                'description' => "The name of the city (required)",
                'value' => $city->name,
                'required' => true
            )
        );

        $form->addElementToEditGroup(
            'text', 'latitude',
            array(
                'id' => 'latitude',
                'label' => 'Latitude',
                'description' => "The latitudinal position of the capital or center of mass (required)",
                'value' => $city->latitude,
                'required' => true
            )
        );

        $form->addElementToEditGroup(
            'text', 'longitude',
            array(
                'id' => 'longitude',
                'label' => 'Longitude',
                'description' => "The longitudinal position of the capital or center of mass (required)",
                'value' => $city->longitude,
                'required' => true
            )
        );

        return $form;
    }


    private function _processForm(SuperEightFestivalsCity $city, $form, $action)
    {
        $this->view->city = $city;

        if ($this->getRequest()->isPost()) {
            try {
                if (!$form->isValid($_POST)) {
                    $this->_helper->flashMessenger('There was an error on the form. Please try again.', 'error');
                    return;
                }
            } catch (Zend_Form_Exception $e) {
                $this->_helper->flashMessenger($e);
            }
            try {
                if ($action == 'delete') {
                    $city->delete();
                    $this->_helper->flashMessenger(__('The city "%s" has been deleted.', $city->name), 'success');
                    $this->redirect("/super-eight-festivals/countries/" . $city->getCountry()->name);
//                    $this->redirect("/super-eight-festivals/countries/" . $city->getCountry()->name . "/cities/");
                } else {
                    $city->setPostData($_POST);
                    if ($city->save()) {
                        if ($action == 'add') {
                            $this->_helper->flashMessenger(__('The city "%s" has been added.', $city->name), 'success');
                        } else if ($action == 'edit') {
                            $this->_helper->flashMessenger(__('The city "%s" has been edited.', $city->name), 'success');
                        }
                        $this->redirect("/super-eight-festivals/countries/" . $city->getCountry()->name . "/cities/" . $city->name);
                        return;
                    }
                }
            } catch (Omeka_Validate_Exception $e) {
                $this->_helper->flashMessenger($e);
            } catch (Omeka_Record_Exception $e) {
                $this->_helper->flashMessenger($e);
            }
        }
    }
}
