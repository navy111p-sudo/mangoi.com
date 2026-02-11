<?php

namespace Kendo\UI;

class UploadAsync extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The selected files will be uploaded immediately by default. You can change this behavior by setting autoUpload to false.
    * @param boolean $value
    * @return \Kendo\UI\UploadAsync
    */
    public function autoUpload($value) {
        return $this->setProperty('autoUpload', $value);
    }

    /**
    * The selected files will be uploaded in separate requests, if this is supported by the browser.
You can change this behavior by setting batch to true, in which case all selected files will be uploaded in one request.The batch mode applies to multiple files, which are selected at the same time.
Files selected one after the other will be uploaded in separate requests.
    * @param boolean $value
    * @return \Kendo\UI\UploadAsync
    */
    public function batch($value) {
        return $this->setProperty('batch', $value);
    }

    /**
    * When the property is set, the selected files are uploaded with the declared size chunk by chunk. Each request sends a separate file blob and additional string metadata to the server. This metadata is in a stringified JSON format and contains the chunkIndex, contentType, totalFileSize, totalChunks, uploadUid properties. These properties enable the validation and combination of the file on the server side. The response also returns a JSON object with the uploaded and fileUid properties, which notifies the client what is the next chunk.You can use this property only when async.batch is set to false.
    * @param float $value
    * @return \Kendo\UI\UploadAsync
    */
    public function chunkSize($value) {
        return $this->setProperty('chunkSize', $value);
    }

    /**
    * By default, the selected files are uploaded one after another. When set to true, all selected files start uploading simultaneously.This property is available when the async.chunkSize is set.
    * @param boolean $value
    * @return \Kendo\UI\UploadAsync
    */
    public function concurrent($value) {
        return $this->setProperty('concurrent', $value);
    }

    /**
    * If you set the property, the failed upload request is repeated after the declared amount of miliseconds.
    * @param float $value
    * @return \Kendo\UI\UploadAsync
    */
    public function autoRetryAfter($value) {
        return $this->setProperty('autoRetryAfter', $value);
    }

    /**
    * Sets the maximum number of attempts that are performed if an upload fails.The property is only used when the async.autoRetryAfter property is also defined.
    * @param float $value
    * @return \Kendo\UI\UploadAsync
    */
    public function maxAutoRetries($value) {
        return $this->setProperty('maxAutoRetries', $value);
    }

    /**
    * The name of the form field submitted to the Remove URL.
    * @param string $value
    * @return \Kendo\UI\UploadAsync
    */
    public function removeField($value) {
        return $this->setProperty('removeField', $value);
    }

    /**
    * The URL of the handler responsible for removing uploaded files (if any). The handler must accept POST
requests containing one or more "fileNames" fields specifying the files to be deleted.
    * @param string $value
    * @return \Kendo\UI\UploadAsync
    */
    public function removeUrl($value) {
        return $this->setProperty('removeUrl', $value);
    }

    /**
    * The HTTP verb to be used by the remove action.
    * @param string $value
    * @return \Kendo\UI\UploadAsync
    */
    public function removeVerb($value) {
        return $this->setProperty('removeVerb', $value);
    }

    /**
    * The name of the form field submitted to the save URL. The default value is the input name.
    * @param string $value
    * @return \Kendo\UI\UploadAsync
    */
    public function saveField($value) {
        return $this->setProperty('saveField', $value);
    }

    /**
    * The URL of the handler that will receive the submitted files. The handler must accept POST requests
containing one or more fields with the same name as the original input name.
    * @param string $value
    * @return \Kendo\UI\UploadAsync
    */
    public function saveUrl($value) {
        return $this->setProperty('saveUrl', $value);
    }

    /**
    * By default, the files are uploaded as filedata. When set to true, the files are read as file buffer by using FileReader and this buffer is send in the request body.
    * @param boolean $value
    * @return \Kendo\UI\UploadAsync
    */
    public function useArrayBuffer($value) {
        return $this->setProperty('useArrayBuffer', $value);
    }

    /**
    * Controls whether to send credentials (cookies, headers) for cross-site requests.
This option will be ignored if the browser doesn't support File API.
    * @param boolean $value
    * @return \Kendo\UI\UploadAsync
    */
    public function withCredentials($value) {
        return $this->setProperty('withCredentials', $value);
    }

//<< Properties
}

?>
