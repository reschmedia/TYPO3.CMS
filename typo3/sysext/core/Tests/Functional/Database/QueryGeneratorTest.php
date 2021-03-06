<?php
declare(strict_types = 1);

namespace TYPO3\CMS\Core\Tests\Functional\Database;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Test case
 */
class QueryGeneratorTest extends FunctionalTestCase
{
    /**
     * @test
     */
    public function getTreeListReturnsIngoingIdIfDepthIsZero(): void
    {
        $id = 1;
        $depth = 0;

        $queryGenerator = new QueryGenerator();
        $treeList = $queryGenerator->getTreeList($id, $depth);

        static::assertSame($id, $treeList);
    }

    /**
     * @test
     */
    public function getTreeListReturnsIngoingIdIfIdIsZero(): void
    {
        $id = 0;
        $depth = 1;

        $queryGenerator = new QueryGenerator();
        $treeList = $queryGenerator->getTreeList($id, $depth);

        static::assertSame($id, $treeList);
    }

    /**
     * @test
     */
    public function getTreeListReturnsPositiveIngoingIdIfIdIsNegative(): void
    {
        $id = -1;
        $depth = 0;

        $queryGenerator = new QueryGenerator();
        $treeList = $queryGenerator->getTreeList($id, $depth);

        static::assertSame(1, $treeList);
    }

    /**
     * @test
     */
    public function getTreeListReturnsEmptyStringIfIdAndDepthAreZeroAndBeginDoesNotEqualZero(): void
    {
        $id = 0;
        $depth = 0;
        $begin = 1;

        $queryGenerator = new QueryGenerator();
        $treeList = $queryGenerator->getTreeList($id, $depth, $begin);

        static::assertSame('', $treeList);
    }

    /**
     * @test
     */
    public function getTreeListReturnsIncomingIdIfNoSubPageRecordsOfThatIdExist(): void
    {
        $id = 1;
        $depth = 1;

        $queryGenerator = new QueryGenerator();
        $treeList = $queryGenerator->getTreeList($id, $depth);

        static::assertSame($id, $treeList);
    }

    /**
     * @test
     */
    public function getTreeListRespectsPermClauses(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/TestGetPageTreeStraightTreeSet.csv');

        $id = 1;
        $depth = 99;

        $queryGenerator = new QueryGenerator();
        $treeList = $queryGenerator->getTreeList($id, $depth, 0, 'hidden = 0');

        static::assertSame('1,2,3,4,5', $treeList);
    }

    /**
     * @test
     * @dataProvider dataForGetTreeListReturnsListOfIdsWithBeginSetToZero
     * @param int $id
     * @param int $depth
     * @param mixed $expectation
     */
    public function getTreeListReturnsListOfIdsWithBeginSetToZero(int $id, int $depth, $expectation): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/TestGetPageTreeStraightTreeSet.csv');

        $queryGenerator = new QueryGenerator();
        $treeList = $queryGenerator->getTreeList($id, $depth);

        static::assertSame($expectation, $treeList);
    }

    /**
     * @return array
     */
    public function dataForGetTreeListReturnsListOfIdsWithBeginSetToZero(): array
    {
        return [
            // [$id, $depth, $expectation]
            [
                1,
                1,
                '1,2'
            ],
            [
                1,
                2,
                '1,2,3'
            ],
            [
                1,
                99,
                '1,2,3,4,5,6'
            ],
            [
                2,
                1,
                '2,3'
            ],
        ];
    }

    /**
     * @test
     * @dataProvider dataForGetTreeListReturnsListOfIdsWithBeginSetToMinusOne
     * @param int $id
     * @param int $depth
     * @param mixed $expectation
     */
    public function getTreeListReturnsListOfIdsWithBeginSetToMinusOne(int $id, int $depth, $expectation): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/TestGetPageTreeStraightTreeSet.csv');

        $queryGenerator = new QueryGenerator();
        $treeList = $queryGenerator->getTreeList($id, $depth, -1);

        static::assertSame($expectation, $treeList);
    }

    /**
     * @return array
     */
    public function dataForGetTreeListReturnsListOfIdsWithBeginSetToMinusOne(): array
    {
        return [
            // [$id, $depth, $expectation]
            [
                1,
                1,
                ',2'
            ],
            [
                1,
                2,
                ',2,3'
            ],
            [
                1,
                99,
                ',2,3,4,5,6'
            ],
            [
                2,
                1,
                ',3'
            ],
        ];
    }

    /**
     * @test
     */
    public function getTreeListReturnsListOfPageIdsOfABranchedTreeWithBeginSetToZero(): void
    {
        $id = 1;
        $depth = 3;

        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/TestGetPageTreeBranchedTreeSet.csv');

        $queryGenerator = new QueryGenerator();
        $treeList = $queryGenerator->getTreeList($id, $depth);

        static::assertSame('1,2,3,4,5', $treeList);
    }

    /**
     * @test
     */
    public function getTreeListReturnsListOfPageIdsOfABranchedTreeWithBeginSetToOne(): void
    {
        $id = 1;
        $depth = 3;
        $begin = 1;

        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/TestGetPageTreeBranchedTreeSet.csv');

        $queryGenerator = new QueryGenerator();
        $treeList = $queryGenerator->getTreeList($id, $depth, $begin);

        static::assertSame('2,3,4,5', $treeList);
    }

    /**
     * @test
     */
    public function getTreeListReturnsListOfPageIdsOfABranchedTreeWithBeginSetToTwo(): void
    {
        $id = 1;
        $depth = 3;
        $begin = 2;

        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/TestGetPageTreeBranchedTreeSet.csv');

        $queryGenerator = new QueryGenerator();
        $treeList = $queryGenerator->getTreeList($id, $depth, $begin);

        static::assertSame('3,5', $treeList);
    }
}
