<?php
declare(strict_types=1);

namespace Farol360\Vestibular2017\Model;

class PageStatistic
{
    public $id_page_statistic;
    public $page_statistic_name;
    public $page_statistic_value;

    public function __construct(array $data = [])
    {
        $this->id_page_statistic = $data['id_page_statistic'] ?? null;
        $this->page_statistic_name = !empty($data['page_statistic_name']) ? strtolower($data['page_statistic_name']) : null;
        $this->page_statistic_value = $data['page_statistic_value'] ?? null;

    }
}
