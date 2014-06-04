<?php

namespace Tagcade\Entity\Report\SourceReport;

use Doctrine\ORM\EntityRepository;

class TrackingTermRepository extends EntityRepository
{
    public function getAllTerms()
    {
        $terms = $this->findAll();

        if (count($terms) === 0) {
            return [];
        }

        $scalarTerms = array_map(function(TrackingTerm $term) {
            return $term->getTerm();
        }, $terms);

        return array_combine($scalarTerms, $terms);
    }
}