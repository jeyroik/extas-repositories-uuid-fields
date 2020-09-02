<?php
namespace extas\components\plugins\repositories;

use extas\components\repositories\FieldAdaptor;
use extas\components\repositories\FieldAdaptorPlugin;
use extas\interfaces\repositories\IFieldAdaptor;
use Ramsey\Uuid\Uuid;

/**
 * Class PluginFieldUuid
 *
 * @package extas\components\plugins\repositories
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginFieldUuid extends FieldAdaptorPlugin
{
    /**
     * @return array
     */
    protected function getMarkers()
    {
        return [
            /**
             * Example: @uuid6
             */
            new class () extends FieldAdaptor implements IFieldAdaptor {

                public function apply(string $value)
                {
                    return Uuid::uuid6()->toString();
                }

                public function isApplicable(string $value): bool
                {
                    return $value === '@uuid6';
                }
            },
            /**
             * Example: @uuid4
             */
            new class () extends FieldAdaptor implements IFieldAdaptor {
                public function apply(string $value)
                {
                    return Uuid::uuid4()->toString();
                }

                public function isApplicable(string $value): bool
                {
                    return $value === '@uuid4';
                }
            },
            /**
             * Example: @uuid5.myNs.myName
             */
            new class () extends FieldAdaptor implements IFieldAdaptor {

                public function apply(string $value)
                {
                    list($marker, $ns, $name) = explode('.', $value);
                    return Uuid::uuid5($ns, $name)->toString();
                }

                public function isApplicable(string $value): bool
                {
                    if (is_string($value) && strpos($value, '.') !== false) {
                        $parts = explode('.', $value);
                        return (count($parts) == 3) && ($parts[0] == '@uuid5');
                    }

                    return false;
                }
            }
        ];
    }
}
