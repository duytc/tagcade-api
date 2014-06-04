<?php

namespace Tagcade\Entity\Report\SourceReport;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="TrackingTermRepository")
 * @ORM\Table(name="tracking_terms")
 */
class TrackingTerm
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     **/
    protected $id;
    /**
     * @ORM\Column(type="string", unique=true)
     * @var string
     */
    protected $term;

    /**
     * @return string
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * @param string $name
     */
    public function setTerm($name)
    {
        $this->term = $name;

        return $this;
    }
}