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
 * Run "APP_ENV=test php -d variables_order=EGPCS -S 127.0.0.1:8000 -t public/"
 * @see http://behat.org/en/latest/quick_start.html
 */
class FeatureContext extends RestContext
{
    const USERS = ["admin" => "admin"];
    const AUTH_URL = "/api/login_check";
    const AUTH_JSON = '
        {
            "username": "%s",
            "password": "%s"
        }
    ';

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
     * @Given I am authenticated as :user
     */
    public function iAmAuthenticatedAs($user)
    {
        $this->request->setHttpHeader("Content-type", "application/ld+json");
        $this->request->send(
            "POST",
            $this->locatePath(session_cache_limiter(self::AUTH_URL)),
            [],
            [],
            sprintf(self::AUTH_JSON, $user, self::USERS[$user])
        );

        $json = json_decode($this->request->getContent(), true);

        // May sure the token was returned
        $this->assertTrue(isset($json["token"]));

        $token = $json["token"];

        $this->request->setHttpHeader("Authorization", "Bearer $token");
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