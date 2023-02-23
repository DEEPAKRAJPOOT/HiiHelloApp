<?php

namespace App\Classes;

class FaceConstraintClass
{
    private $face = null;
    private $message = null;
    private $gender = null;
    private $min_age = 15;
    private $celeb = null;
    private $user = null;
    private $isFace = true;
    public function __construct($user, $face)
    {
        $this->face = $face;
        $this->user = $user;
    }

    public function setCeleb($celeb)
    {
        $this->celeb = $celeb;
        return $this;
    }

    public function isFaceDetected()
    {
        if (count($this->face['FaceDetails']) === 0) {
            $this->isFace = false;
            $this->message = "No Face detected, ";
        }
        return $this;
    }

    public function isMultipleFaces()
    {
        if (count($this->face['FaceDetails']) > 1) {
            $this->message .= "Multiple Face detected, ";
        }
        return $this;
    }

    public function isGenderCompliant()
    {

        if ($this->isFace) {
            $this->gender = $this->face['FaceDetails'][0]['Gender']['Value'];
            if ($this->user->gender != $this->gender) {
                $this->message .= "User gender not matched, ";
            }
        }
        return $this;
    }

    public function isAgeCompliant()
    {

        if ($this->isFace) {
            $low = $this->face['FaceDetails'][0]['AgeRange']['Low'];
            $high =  $this->face['FaceDetails'][0]['AgeRange']['High'];
            $age = ceil(($high + $low) / 2) ?? 0;
            if ($age <= $this->min_age) {
                $this->message .= "Age less than " . $this->min_age . " detected, ";
            }
        }
        return $this;
    }

    public function isCelebDetected()
    {
        if (count($this->celeb['CelebrityFaces']) > 0) {
            $celebrity_name = $this->celeb['CelebrityFaces'][0]['Name'];
            $this->message .= "Celebrity face detected. Name: " . $celebrity_name .", ";
        }
        return $this;
    }


    public function getMessage()
    {
        return $this->message;
    }

    public function getDimension()
    {
        if ($this->isFace) {
            $leftEyeBrowUp_X = $this->face['FaceDetails'][0]['Landmarks'][7]['X'];
            $leftEyeBrowUp_Y = $this->face['FaceDetails'][0]['Landmarks'][7]['Y'];
            $rightEyeBrowUp_X = $this->face['FaceDetails'][0]['Landmarks'][10]['X'];
            $mouthLeft_Y = $this->face['FaceDetails'][0]['Landmarks'][2]['Y'];
            $w = ($rightEyeBrowUp_X - $leftEyeBrowUp_X);
            $h = ($mouthLeft_Y - $leftEyeBrowUp_Y);
            return ($w / $h);
        }

        return null;
    }

    public function getFaceCount()
    {
        return count($this->face['FaceDetails']);
    }
}
