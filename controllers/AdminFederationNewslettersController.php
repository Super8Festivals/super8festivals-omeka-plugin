<?php

class SuperEightFestivals_AdminFederationNewslettersController extends Omeka_Controller_AbstractActionController
{

    public function indexAction()
    {
        $this->redirect("/super-eight-festivals/federation/");
    }

    public function addAction()
    {
        $request = $this->getRequest();

        $newsletter = new SuperEightFestivalsFederationNewsletter();
        $form = $this->_getForm($newsletter);
        $this->view->form = $form;
        $this->view->newsletter = $newsletter;
        $this->_processForm($newsletter, $form, 'add');
    }

    public function editAction()
    {
        $request = $this->getRequest();

        $newsletterID = $request->getParam('newsletterID');
        $newsletter = SuperEightFestivalsFederationNewsletter::get_by_id($newsletterID);
        $this->view->newsletter = $newsletter;

        $form = $this->_getForm($newsletter);
        $this->view->form = $form;
        $this->_processForm($newsletter, $form, 'edit');
    }

    public function deleteAction()
    {
        $request = $this->getRequest();

        $newsletterID = $request->getParam('newsletterID');
        $newsletter = SuperEightFestivalsFederationNewsletter::get_by_id($newsletterID);
        $this->view->newsletter = $newsletter;

        $form = $this->_getDeleteForm();
        $this->view->form = $form;
        $this->_processForm($newsletter, $form, 'delete');
    }

    protected function _getForm(SuperEightFestivalsFederationNewsletter $newsletter = null): Omeka_Form_Admin
    {
        $formOptions = array(
            'type' => 'super_eight_festivals_federation_newsletter'
        );

        $form = new Omeka_Form_Admin($formOptions);

        $file = $newsletter->get_file();

        $form->addElementToEditGroup(
            'select', 'contributor_id',
            array(
                'id' => 'contributor_id',
                'label' => 'Contributor',
                'description' => "The person who contributed the item",
                'multiOptions' => get_parent_contributor_options(),
                'value' => $file ? $file->contributor_id : null,
                'required' => false,
            )
        );

        $form->addElementToEditGroup(
            'text', 'title',
            array(
                'id' => 'title',
                'label' => 'Title',
                'description' => "The federation newsletter's title",
                'value' => $file ? $file->title : "",
                'required' => false,
            )
        );

        $form->addElementToEditGroup(
            'text', 'description',
            array(
                'id' => 'description',
                'label' => 'Description',
                'description' => "The federation newsletter's description",
                'value' => $file ? $file->description : "",
                'required' => false,
            )
        );

        $form->addElementToEditGroup(
            'file', 'file',
            array(
                'id' => 'file',
                'label' => 'File',
                'description' => "The document/image file",
                'required' => !$file || $file->file_name == "" || !file_exists($file->get_path()),
                'accept' => get_form_accept_string(array_merge(get_image_types(), get_document_types())),
            )
        );

        return $form;
    }

    private function _processForm(SuperEightFestivalsFederationNewsletter $newsletter, Zend_Form $form, $action)
    {
        $this->view->newsletter = $newsletter;

        // form can only be processed by POST request
        if (!$this->getRequest()->isPost()) {
            return;
        }

        // Validate form
        try {
            if (!$form->isValid($_POST)) {
                $this->_helper->flashMessenger('Invalid form data', 'error');
                return;
            }
        } catch (Zend_Form_Exception $e) {
            $this->_helper->flashMessenger("An error occurred while submitting the form: {$e->getMessage()}", 'error');
            return;
        }

        $fileInputName = "file";
        try {
            switch ($action) {
                case "add":
                    $newsletter->setPostData($_POST);
                    $newsletter->save(true);

                    $file = $newsletter->upload_file($fileInputName);
                    $file->contributor_id = $this->getParam("contributor", 0);
                    $file->title = $this->getParam("title", "");
                    $file->description = $this->getParam("description", "");
                    $file->save();

                    $this->_helper->flashMessenger("Federation Newsletter successfully added.", 'success');
                    break;
                case "edit":
                    $newsletter->setPostData($_POST);
                    $newsletter->save(true);

                    // get the original record so that we can use old information which doesn't persist (e.g. files)
                    $originalRecord = SuperEightFestivalsFederationNewsletter::get_by_id($newsletter->id);
                    $newsletter->file_id = $originalRecord->file_id;

                    // only change files if there is a file waiting
                    if (has_temporary_file($fileInputName)) {
                        // delete old files
                        $originalFile = $originalRecord->get_file();
                        $originalFile->delete_files();

                        // upload new file
                        $file = $newsletter->upload_file($fileInputName);
                        $file->contributor_id = $this->getParam("contributor", 0);
                        $file->title = $this->getParam("title", "");
                        $file->description = $this->getParam("description", "");
                        $file->save();
                    } else {
                        $file = $originalRecord->get_file();
                        $file->contributor_id = $this->getParam("contributor", 0);
                        $file->title = $this->getParam("title", "");
                        $file->description = $this->getParam("description", "");
                        $file->save();
                    }

                    // display result dialog
                    $this->_helper->flashMessenger("Federation Newsletter successfully updated.", 'success');
                    break;
                case "delete":
                    $newsletter->delete();
                    $this->_helper->flashMessenger("Federation Newsletter successfully deleted.", 'success');
                    break;
            }
        } catch (Omeka_Record_Exception $e) {
            $this->_helper->flashMessenger($e->getMessage(), 'error');
        } catch (Omeka_Validate_Exception $e) {
            $this->_helper->flashMessenger($e->getMessage(), 'error');
        }

        $this->redirect("/super-eight-festivals/federation/");
    }

}