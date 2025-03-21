<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Clickhouse;

use AlephTools\SqlBuilder\Clickhouse\Clause\ApplyClause;
use AlephTools\SqlBuilder\Clickhouse\Clause\ArrayJoinClause;
use AlephTools\SqlBuilder\Clickhouse\Clause\ExceptClause;
use AlephTools\SqlBuilder\Clickhouse\Clause\ExceptQueryClause;
use AlephTools\SqlBuilder\Clickhouse\Clause\FormatClause;
use AlephTools\SqlBuilder\Clickhouse\Clause\IntersectClause;
use AlephTools\SqlBuilder\Clickhouse\Clause\IntoOutfileClause;
use AlephTools\SqlBuilder\Clickhouse\Clause\JoinClause;
use AlephTools\SqlBuilder\Clickhouse\Clause\PreWereClause;
use AlephTools\SqlBuilder\Clickhouse\Clause\QualifyClause;
use AlephTools\SqlBuilder\Clickhouse\Clause\ReplaceClause;
use AlephTools\SqlBuilder\Clickhouse\Clause\SampleClause;
use AlephTools\SqlBuilder\Clickhouse\Clause\SettingsClause;
use AlephTools\SqlBuilder\Clickhouse\Clause\WindowClause;
use AlephTools\SqlBuilder\Sql\AbstractSelectStatement;

class SelectStatement extends AbstractSelectStatement
{
    use SampleClause;
    use PreWereClause;
    use QualifyClause;
    use IntersectClause;
    use ExceptClause;
    use ExceptQueryClause;
    use IntoOutfileClause;
    use FormatClause;
    use SettingsClause;
    use ApplyClause;
    use ReplaceClause;
    use WindowClause;
    use JoinClause;
    use ArrayJoinClause;

    public function copy(): static
    {
        $copy = new static($this->db);

        $this->cloneApply($copy);
        $this->cloneArrayJoin($copy);
        $this->cloneExcept($copy);
        $this->cloneExceptQuery($copy);
        $this->cloneFormat($copy);
        $this->cloneFrom($copy);
        $this->cloneGroupBy($copy);
        $this->cloneHaving($copy);
        $this->cloneIntersect($copy);
        $this->cloneIntoOutfile($copy);
        $this->cloneJoin($copy);
        $this->cloneLimit($copy);
        $this->cloneLock($copy);
        $this->cloneOffset($copy);
        $this->cloneOrderBy($copy);
        $this->clonePreWhere($copy);
        $this->cloneQualify($copy);
        $this->cloneReplace($copy);
        $this->cloneSample($copy);
        $this->cloneSelect($copy);
        $this->cloneSettings($copy);
        $this->cloneWhere($copy);
        $this->cloneWindow($copy);
        $this->cloneWith($copy);

        return $copy;
    }

    public function clean(): static
    {
        $this->cleanApply();
        $this->cleanArrayJoin();
        $this->cleanExcept();
        $this->cleanExceptQuery();
        $this->cleanFormat();
        $this->cleanFrom();
        $this->cleanGroupBy();
        $this->cleanHaving();
        $this->cleanIntersect();
        $this->cleanIntoOutfile();
        $this->cleanJoin();
        $this->cleanLimit();
        $this->cleanLock();
        $this->cleanOffset();
        $this->cleanOrderBy();
        $this->cleanPreWhere();
        $this->cleanQualify();
        $this->cleanReplace();
        $this->cleanSample();
        $this->cleanSelect();
        $this->cleanSettings();
        $this->cleanWhere();
        $this->cleanWindow();
        $this->cleanWith();

        return $this;
    }

    public function build(): static
    {
        if ($this->built) {
            return $this;
        }
        $this->sql = '';
        $this->params = [];
        if ($this->union) {
            $this->buildUnion();
            $this->buildOrderBy();
            $this->buildLimit();
            $this->buildOffset();
        } else {
            $this->buildWith();
            $this->buildSelect();
            $this->buildReplace();
            $this->buildApply();
            $this->buildExcept();
            $this->buildFrom();
            $this->buildJoin();
            $this->buildPreWhere();
            $this->buildWhere();
            $this->buildSample();
            $this->buildExceptQuery();
            $this->buildGroupBy();
            $this->buildHaving();
            $this->buildWindow();
            $this->buildQualify();
            $this->buildOrderBy();
            $this->buildLimit();
            $this->buildOffset();
            $this->buildIntersect();
            $this->buildArrayJoin();
            $this->buildIntoOutfile();
            $this->buildFormat();
            $this->buildLock();
            $this->buildSettings();
        }
        $this->built = true;
        return $this;
    }
}
