<?php
namespace thewulf7\friendloc\components\router;


use thewulf7\friendloc\components\router\urlrules\iUrlRule;
use thewulf7\friendloc\components\router\urlrules\RestUrlRule;
use thewulf7\friendloc\components\router\urlrules\SimpleUrlRule;

class UrlRule
{
    const SIMPLE_RULE = 'simpleRule';

    const REST_RULE = 'restRule';

    protected function createRule($arRule): iUrlRule
    {
        $rule = $arRule['rule'] ?? self::SIMPLE_RULE;
        switch($rule)
        {
            case self::SIMPLE_RULE:
                $routeRule = new SimpleUrlRule($arRule);
                break;
            case self::REST_RULE:
                $routeRule = new RestUrlRule($arRule);
                break;
            default:
                throw new \InvalidArgumentException("$rule not found");
                break;
        }

        return $routeRule;
    }

    public function create($arRule): iUrlRule
    {
        return $this->createRule($arRule);
    }
}