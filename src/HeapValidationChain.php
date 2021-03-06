<?php

namespace ObjectivePHP\Validation;

use ObjectivePHP\Notification\Stack;
use ObjectivePHP\Primitives\Collection\Collection;
use ObjectivePHP\Validation\Exception\ValidationException;
use ObjectivePHP\Validation\Rule\AbstractValidationRule;
use ObjectivePHP\Validation\Rule\ValidationRuleInterface;

/**
 * Class ValidationChain
 *
 * @package ObjectivePHP\Validation
 */
class HeapValidationChain extends AbstractValidationRule implements HeapValidationChainInterface
{
    /**
     * @var Collection
     */
    protected $rules;

    /**
     * HeapValidationChain constructor.
     */
    public function __construct()
    {
        $this->rules = (new Collection);
        $this->init();
    }

    /**
     * To be overriden in inherited classes
     */
    protected function init()
    {
    }

    /**
     * Register a validation rule
     *
     * @param string                  $key
     * @param ValidationRuleInterface $rule
     *
     * @return $this
     */
    public function registerRule(string $key, ValidationRuleInterface $rule)
    {
        if (!$this->rules->has($key)) {
            $this->rules->set($key, new ValidationChain());
        }

        $this->rules->get($key)->registerRule($rule);

        return $this;
    }

    /**
     * Get Rules
     *
     * @return Collection
     */
    public function getRules() : Collection
    {
        return $this->rules;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($heap, $context = null) : bool
    {
        if (!is_array($heap)
            && (!$heap instanceof \Iterator
                && !$heap instanceof \ArrayAccess
                && !$heap instanceof \ArrayObject
            )
        ) {
            throw new ValidationException(__METHOD__ . ' expects data to be an array or ArrayObject like structure');
        }

        $isValid = true;

        /** @var ValidationRuleInterface $rule */
        foreach ($this->getRules() as $key => $rule) {
            if (array_key_exists($key, $heap) || isset($heap[$key])) {
                $data = $heap[$key];
                if (!$rule->validate($data, $context)) {
                    if ($this->getNotifications()->lacks($key)) {
                        $this->getNotifications()->set($key, new Stack());
                    }

                    $this->getNotifications()->get($key)->merge($rule->getNotifications());

                    $isValid = false;
                }
            }
        }

        return $isValid;
    }
}
