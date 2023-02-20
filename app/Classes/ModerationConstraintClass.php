<?php

namespace App\Classes;

class ModerationConstraintClass
{
    private $moderation = null;
    private $message = null;
    private $moderation_labels_data = null;
    private $constraints = [];
    public function __construct($moderation)
    {
        $this->moderation = $moderation;
        $this->constraints = config('utility.aws_image_moderation.category_filter', []);
    }
    public function isModerationDetected()
    {
        if (count($this->moderation['ModerationLabels']) > 0) {
            foreach ($this->moderation['ModerationLabels'] as $cat_key => $res_data) {

                if (array_key_exists($res_data['Name'], $this->constraints) && $res_data['Confidence'] >= $this->constraints[$res_data['Name']]) {
                    $this->moderation_labels_data = $res_data['Name'] . " value in setting (" . $this->constraints[$res_data['Name']] . "). In response confidence value (" . $res_data['Confidence'] . ")";

                    $this->message = "Image Contain " . $res_data['Name'] . " With Confidence value " . $res_data['Confidence'];
                    break;
                }
            }
        }
        return $this;
    }
    public function getMessage()
    {
        return $this->message;
    }

    public function getModerationRequest()
    {
        return ($this->moderation["@metadata"]);
    }

    public function getModerationResponse()
    {
        return ($this->moderation["ModerationLabels"]);
    }

    public function getModerationLabelData()
    {
        return $this->moderation_labels_data;
    }
}
