<?php

namespace Oro\Bundle\DataGridBundle\Datagrid;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestParameters
{
    const ADDITIONAL_PARAMETERS = '_parameters';
    const DEFAULT_ROOT_PARAM = 'grid';

    /** @var string */
    protected $rootParam;

    /** @var RequestStack */
    protected $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * Get parameter value from parameters container
     *
     * @param  string $type
     * @param  mixed  $default
     *
     * @return mixed
     */
    public function get(string $type, $default = [])
    {
        $rootParameter = $this->getRootParameterValue();

        return isset($rootParameter[$type]) ? $rootParameter[$type] : $default;
    }

    /**
     * @param  string $type
     * @param  mixed  $value
     */
    public function set(string $type, $value): void
    {
        $parameters = $this->getRootParameterValue();
        $currentValue = $this->get($type);

        if (is_array($currentValue) && is_array($value)) {
            $parameters[$type] = array_replace_recursive($currentValue, $value);
        } else {
            $parameters[$type] = $value;
        }

        $this->getRequest()->query->set($this->rootParam ? : self::DEFAULT_ROOT_PARAM, $parameters);
    }

    public function getLocale(): string
    {
        return $this->getRequest()->getLocale();
    }

    /**
     * @param $rootParam
     *
     * @return $this
     */
    public function setRootParameter(string $rootParam): self
    {
        $this->rootParam = $rootParam;

        return $this;
    }

    public function getRootParameterValue(): array
    {
        return $this->getRequest()->get($this->rootParam ? : self::DEFAULT_ROOT_PARAM, []);
    }

    /**
     * @return null|Request
     */
    protected function getRequest(): ?Request
    {
        return $this->requestStack->getCurrentRequest();
    }
}
