<?php

namespace App\Classes;

use Aws\Rekognition\RekognitionClient;
use App\Classes\FaceConstraintClass;
use App\Classes\ModerationConstraintClass;
use App\Classes\TextConstraintClass;

class ImageDetectionClass
{
    private $aws_instance = null;
    private $bytes = null;
    private $user = null;
    private $response = [
        "is_safe_image" => true,
        "moderation_labels_data" => null,
        "log_message" => null,
        "image_moderation_request" => null,
        "image_moderation_response" => null,
        "moderation_response" => [],
        "total_face_detected" => 0,
        "Facial_Width_Height_ratio" => 0,
        "face_detected_message" => null
    ];

    public function __construct($image_data, $user, $isFile = true)
    {
        $this->user = $user;

        if ($isFile) {
            //FILE OBJECT 
            $image = fopen($image_data->getPathName(), 'r');
            $this->bytes = fread($image, $image_data->getSize());
        } else {
            //image_param_name = S3 image url will be here as parameter if check type is url
            $image_path =   $image_data;
            $this->bytes  = file_get_contents($image_path);
        }

        $this->aws_instance = new RekognitionClient([
            'region'    => 'ap-south-1',
            'version'   => 'latest'
        ]);
    }

    private function followFacialConstraint()
    {
        $face_instance = $this->aws_instance->detectFaces([
            'Attributes' => ['ALL'], //ALL, DEFAULT
            'Image'         => ['Bytes' => $this->bytes],
        ]);


        $face = new FaceConstraintClass($this->user, $face_instance);
        $message = $face->isFaceDetected()
            // ->isMultipleFaces()
            // ->isGenderCompliant()
            ->isAgeCompliant();


        $this->response['total_face_detected'] = $message->getFaceCount();
        $this->response['Facial_Width_Height_ratio'] = $message->getDimension();

        if ($message->getMessage()) {
            $this->response['is_safe_image'] = false;
            $this->response['face_detected_message'] = $message->getMessage();
            $this->response['log_message'] = $message->getMessage();
            return $this->response;
        }

        $celeb = $this->aws_instance->recognizeCelebrities([
            'Image' => [ // REQUIRED
                //'Bytes' => file_get_contents("1.jpg"),
                'Bytes' => $this->bytes,
            ],
            'MaxLabels' => 10,
            'MinConfidence' => 20,
        ]);

        $message = $message->setCeleb($celeb)->isCelebDetected();
        if ($message->getMessage()) {
            $this->response['is_safe_image'] = false;
            $this->response['face_detected_message'] = $message->getMessage();
            $this->response['log_message'] = $message->getMessage();
        }

        return $this->response;
    }

    private function followTextConstraint()
    {
        $text_instance = $this->aws_instance->detectText([
            'Image' => [ // REQUIRED
                'Bytes' => $this->bytes,
            ],
            'MaxLabels' => 10,
            'MinConfidence' => 90,
        ]);

        $text = new TextConstraintClass($text_instance);
        $text = $text->isTextDetected();
        if ($text->getMessage()) {
            $this->response['is_safe_image'] = false;
            $this->response['log_message'] = $text->getMessage();
        }
        return $this->response;
    }

    private function followModerationConstraint()
    {
        $min_confidence = config('utility.aws_image_moderation.min_confidence', 70);
        $moderation_constraint = $this->aws_instance->detectModerationLabels([
            'Image'         => ['Bytes' => $this->bytes],
            'MinConfidence' => $min_confidence
        ]);

        $moderation = new ModerationConstraintClass($moderation_constraint);
        $moderation = $moderation->isModerationDetected();
        if ($moderation->getMessage()) {
            $this->response['is_safe_image'] = false;
            $this->response['image_moderation_request'] = json_encode($moderation->getModerationRequest());
            $this->response['image_moderation_response'] = json_encode($moderation->getModerationResponse());
            $this->response['moderation_response'] = $moderation->getModerationResponse();
            $this->response['moderation_labels_data'] = $moderation->getModerationLabelData();
            $this->response['log_message'] = $moderation->getMessage();
        }

        return $this->response;
    }


    public function checkConstraints()
    {

        $this->followFacialConstraint();

        if (!$this->response['is_safe_image']) {
            return $this->response;
        }

        $this->followTextConstraint();
        if (!$this->response['is_safe_image']) {
            return $this->response;
        }

        $this->followModerationConstraint();

        return $this->response;
    }
}
