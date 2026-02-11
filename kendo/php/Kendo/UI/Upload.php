<?php

namespace Kendo\UI;

class Upload extends \Kendo\UI\Widget {
    protected function name() {
        return 'Upload';
    }

    protected function createElement() {
        $element = new \Kendo\Html\Element('input', true);

        $element->attr('type', 'file');

        return $element;
    }

    /**
    * List of files to be initially rendered in the Upload widget files list.
    * @param array $value
    * @return \Kendo\UI\Upload
    */
    public function files($value) {
        return $this->setProperty('files', $value);
    }

//>> Properties

    /**
    * Configures the ability to upload a files in an asynchronous manner. Please refer to the
async mode help topic
for more details.
    * @param \Kendo\UI\UploadAsync|array $value
    * @return \Kendo\UI\Upload
    */
    public function async($value) {
        return $this->setProperty('async', $value);
    }

    /**
    * Enables the selection of folders instead of files. When the user selects a directory, its entire content hierarchy of files is included in the set of selected items. The setting supported only in browsers that support webkitdirectory.
    * @param boolean $value
    * @return \Kendo\UI\Upload
    */
    public function directory($value) {
        return $this->setProperty('directory', $value);
    }

    /**
    * Enables the dropping of folders over the Upload and its drop zone. When a directory is dropped, its entire content hierarchy of files is included in the set of selected items. This setting is supported only in browsers that support DataTransferItem and webkitGetAsEntry.
    * @param boolean $value
    * @return \Kendo\UI\Upload
    */
    public function directoryDrop($value) {
        return $this->setProperty('directoryDrop', $value);
    }

    /**
    * Initializes a dropzone elements based on a given selector that provides drag and drop file upload.
    * @param string $value
    * @return \Kendo\UI\Upload
    */
    public function dropZone($value) {
        return $this->setProperty('dropZone', $value);
    }

    /**
    * Enables (true) or disables (false) an Upload. A disabled
Upload may be re-enabled via enable().
    * @param boolean $value
    * @return \Kendo\UI\Upload
    */
    public function enabled($value) {
        return $this->setProperty('enabled', $value);
    }

    /**
    * Adds UploadFile to the Upload.
    * @param \Kendo\UI\UploadFile|array,... $value one or more UploadFile to add.
    * @return \Kendo\UI\Upload
    */
    public function addFile($value) {
        return $this->add('files', func_get_args());
    }

    /**
    * Sets the strings rendered by the Upload.
    * @param \Kendo\UI\UploadLocalization|array $value
    * @return \Kendo\UI\Upload
    */
    public function localization($value) {
        return $this->setProperty('localization', $value);
    }

    /**
    * Enables (true) or disables (false) the ability to select multiple files.
If false, users will be able to select only one file at a time. Note: This option does not
limit the total number of uploaded files in an asynchronous configuration.
    * @param boolean $value
    * @return \Kendo\UI\Upload
    */
    public function multiple($value) {
        return $this->setProperty('multiple', $value);
    }

    /**
    * Enables (true) or disables (false) the ability to display a file listing
for uploading a files. Disabling a file listing may be useful you wish to customize the UI; use the
client-side events to build your own UI.
    * @param boolean $value
    * @return \Kendo\UI\Upload
    */
    public function showFileList($value) {
        return $this->setProperty('showFileList', $value);
    }

    /**
    * Sets the template option of the Upload.
    * The template used to render the files in the list
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\Upload
    */
    public function templateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('template', $value);
    }

    /**
    * Sets the template option of the Upload.
    * The template used to render the files in the list
    * @param string $value The template content.
    * @return \Kendo\UI\Upload
    */
    public function template($value) {
        return $this->setProperty('template', $value);
    }

    /**
    * Configures the validation options for uploaded files.
    * @param \Kendo\UI\UploadValidation|array $value
    * @return \Kendo\UI\Upload
    */
    public function validation($value) {
        return $this->setProperty('validation', $value);
    }

    /**
    * Sets the cancel event of the Upload.
    * Fires when the upload has been cancelled while in progress.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Upload
    */
    public function cancel($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('cancel', $value);
    }

    /**
    * Sets the clear event of the Upload.
    * Triggered when files are cleared by clicking on the "Clear" button. Note: Cancelling this event will prevent the clearing the selected files.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Upload
    */
    public function clear($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('clear', $value);
    }

    /**
    * Sets the complete event of the Upload.
    * Fires when all active uploads have completed either successfully or with errors.Note: The complete event fires only when the upload is in
async mode.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Upload
    */
    public function complete($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('complete', $value);
    }

    /**
    * Sets the error event of the Upload.
    * Fires when an upload / remove operation has failed.Note: The error event fires only when the upload is in
async mode.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Upload
    */
    public function error($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('error', $value);
    }

    /**
    * Sets the pause event of the Upload.
    * Triggered when the files are cleared by clicking the Pause button. The button is visible if chunksize is set.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Upload
    */
    public function pause($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('pause', $value);
    }

    /**
    * Sets the progress event of the Upload.
    * Fires when the data about the progress of the upload is available.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Upload
    */
    public function progress($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('progress', $value);
    }

    /**
    * Sets the resume event of the Upload.
    * Fires when the files are resumed through clicking the Resume button. The button is visible if chunksize is set and the file upload is paused.The following example demonstrates how to wire up an event handler that is triggered when a user resumes a selected file.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Upload
    */
    public function resume($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('resume', $value);
    }

    /**
    * Sets the remove event of the Upload.
    * Fires when an uploaded file is about to be removed. If the event is canceled, the remove operation is prevented.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Upload
    */
    public function remove($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('remove', $value);
    }

    /**
    * Sets the select event of the Upload.
    * Triggered when a files is selected. Note: Cancelling this event will prevent the selection from
occurring.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Upload
    */
    public function select($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('select', $value);
    }

    /**
    * Sets the success event of the Upload.
    * Fires when an upload / remove operation has been completed successfully.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Upload
    */
    public function success($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('success', $value);
    }

    /**
    * Sets the upload event of the Upload.
    * Fires when one or more files are about to be uploaded.
Canceling the event will prevent the upload.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Upload
    */
    public function upload($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('upload', $value);
    }


//<< Properties
}

?>
