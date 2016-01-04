<?php

namespace Fi\CoreBundle\DependencyInjection;

class jsonResponse {

    private $errcode = -123456789;
    private $message = "";
    private $parms = array();

    public function __construct($errcode, $message, $parms = null) {
        $this->errcode = $errcode;
        $this->message = $message;
        if ($parms) {
            $this->parms = $parms;
        }
    }

    public function __toString() {
        return $this->getEncodedResponse();
    }

    public function getEncodedResponse() {
        return json_encode(array("errcode" => $this->errcode, "message" => $this->message, "parms" => $this->parms));
    }

    public function getArrayResponse() {
        return array("errcode" => $this->errcode, "message" => $this->message, "parms" => $this->parms);
    }

}

?>
