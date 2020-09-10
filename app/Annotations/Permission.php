<?php


namespace App\Annotations;

use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;
use Illuminate\Support\Arr;

/**
 * @Annotation
 * @Target({"METHOD"})
 * Class Permission
 * @package App\Annotations
 * @Attributes({
 *  @Attribute("stringProperty", type = "string")
 *  })
 */
class Permission
{
    /** @Required  */
    public $action;

    public function __construct(array $values)
    {
        $this->action = Arr::get($values, 'action');
    }
}
