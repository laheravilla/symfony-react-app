<?php

use App\DataFixtures\AppFixtures;
use Behatch\Context\RestContext;
use Behatch\HttpCall\Request;
use Coduo\PHPMatcher\PHPMatcher;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * @see http://behat.org/en/latest/quick_start.html
 */
class FeatureContext extends RestContext
{
    /** @var AppFixtures */
    private $fixtures;
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var PHPMatcher */
    private $matcher;

    public function __construct(
        Request $request,
        AppFixtures $fixtures,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($request);
        $this->fixtures = $fixtures;
        $this->entityManager = $entityManager;
        $this->matcher = new PHPMatcher();
    }

    /**
     * @BeforeScenario @createSchema
     */
    public function createSchema()
    {
        // Get entity metadata
        $classes = $this->entityManager->getMetadataFactory()->getAllMetadata();

        // Drop and create schema
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropSchema($classes);
        $schemaTool->createSchema($classes);

        // Load Fixtures... and execute
        $purger = new ORMPurger($this->entityManager);
        $fixturesExecutor = new ORMExecutor($this->entityManager, $purger);
        $fixturesExecutor->execute([
            $this->fixtures
        ]);
    }
}