<?php

namespace App\Json;

use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;

/**
 * Handler for JMSSerializer which simply tries to instanciate a
 * new \DateTime object based on the data
 */
class DateTimeHandler implements SubscribingHandlerInterface {

    /**
     * @return mixed
     */
    public static function getSubscribingMethods() {
        return [
            [
                'type' => 'DateTime',
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'method' => 'deserializeDateTimeFromJson'
            ]
        ];
    }

    public function deserializeDateTimeFromJson(JsonDeserializationVisitor $visitor, $data, array $type) {
        if($data === null) {
            return $data;
        }

        return new \DateTime((string)$data);
    }
}