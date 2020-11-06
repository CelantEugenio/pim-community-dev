<?php

namespace Oro\Bundle\DataGridBundle\Extension\Pager\Orm;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Oro\Bundle\DataGridBundle\Extension\Pager\AbstractPager;
use Oro\Bundle\DataGridBundle\Extension\Pager\PagerInterface;
use Oro\Bundle\DataGridBundle\ORM\Query\QueryCountCalculator;

class Pager extends AbstractPager implements PagerInterface
{
    /** @var QueryBuilder */
    protected $qb;

    /** @var array */
    protected $parameters = [];

    public function __construct(int $maxPerPage = 10, QueryBuilder $qb = null)
    {
        $this->qb = $qb;
        parent::__construct($maxPerPage);
    }

    /**
     * @param QueryBuilder $qb
     *
     * @return $this
     */
    public function setQueryBuilder(QueryBuilder $qb): self
    {
        $this->qb = $qb;

        return $this;
    }

    public function getQueryBuilder(): \Doctrine\ORM\QueryBuilder
    {
        return $this->qb;
    }

    /**
     * Calculates count
     */
    public function computeNbResult(): int
    {
        $qb = clone $this->getQueryBuilder();
        $query = $qb->setFirstResult(null)
            ->setMaxResults(null)
            ->resetDQLPart('orderBy')
            ->getQuery();

        return QueryCountCalculator::calculateCount($query);
    }

    /**
     * {@inheritdoc}
     */
    public function getResults($hydrationMode = Query::HYDRATE_OBJECT): array
    {
        return $this->getQueryBuilder()->getQuery()->execute([], $hydrationMode);
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        $this->resetIterator();

        $this->setNbResults($this->computeNbResult());

        /** @var QueryBuilder $query */
        $query = $this->getQueryBuilder();

        $query->setFirstResult(null);
        $query->setMaxResults(null);

        if (count($this->getParameters()) > 0) {
            $query->setParameters($this->getParameters());
        }

        if (0 == $this->getPage() || 0 == $this->getMaxPerPage() || 0 == $this->getNbResults()) {
            $this->setLastPage(0);
        } else {
            $offset = ($this->getPage() - 1) * $this->getMaxPerPage();

            $this->setLastPage(ceil($this->getNbResults() / $this->getMaxPerPage()));

            $query->setFirstResult($offset);
            $query->setMaxResults($this->getMaxPerPage());
        }
    }

    /**
     * Returns the current pager's parameter holder.
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Returns a parameter.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getParameter(string $name, $default = null)
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : $default;
    }

    /**
     * Checks whether a parameter has been set.
     *
     * @param string $name
     */
    public function hasParameter(string $name): bool
    {
        return isset($this->parameters[$name]);
    }

    /**
     * Sets a parameter.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function setParameter(string $name, $value): void
    {
        $this->parameters[$name] = $value;
    }
}
