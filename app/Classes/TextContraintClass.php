<?php
namespace App\Classes;

class TextConstraintClass
{
    private $text = null;
    private $message = null;
    public function __construct($text)
    {
        $this->text = $text;
    }
    public function isTextDetected()
    {
        if (count($this->text['TextDetections']) > 0) {
            $this->message = "Image has texts ".$this->text['TextDetections'][0]["DetectedText"];
        }
        return $this;
    }
    public function getMessage()
    {
        return $this->message;
    }
}
