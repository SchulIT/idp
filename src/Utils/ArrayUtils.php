<?php

declare(strict_types=1);

namespace App\Utils;

use App\Form\DataTransformer\KeyValueContainer;
use Closure;
use InvalidArgumentException;
use Traversable;

class ArrayUtils {
    public static function apply(array &$items, Closure $closure): void {
        foreach($items as $item) {
            $closure($item);
        }
    }

    public static function createArray(array $keys, array $values): array {
        $array = [ ];
        $count = count($keys);

        $keys = array_values($keys);
        $values = array_values($values);

        if(count($keys) !== count($values)) {
            throw new InvalidArgumentException('$keys and $items parameter need to have the same length.');
        }

        for($i = 0; $i < $count; ++$i) {
            $array[$keys[$i]] = $values[$i];
        }

        return $array;
    }

    public static function createArrayWithKeys(array $items, Closure $keyFunc): array {
        $array = [ ];

        foreach($items as $item) {
            $key = $keyFunc($item);
            $array[$key] = $item;
        }

        return $array;
    }

    /**
     * Extract an array out of $data or throw an exception if not possible.
     *
     * @param iterable|KeyValueContainer $data Something that can be converted to an array.
     *
     * @return array Native array representation of $data
     *
     * @throws InvalidArgumentException If $data can not be converted to an array.
     */
    public static function iterableKeyValueContainerToArray(KeyValueContainer|iterable $data): array {
        if (is_array($data)) {
            return $data;
        }

        if ($data instanceof KeyValueContainer) {
            return $data->toArray();
        }
        return iterator_to_array($data);
    }
}
