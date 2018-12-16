<?php

use App\DataFixtures\AppFixtures;
use Behat\Gherkin\Node\PyStringNode;
use Behatch\Context\RestContext;
use Behatch\HttpCall\Request;
use Coduo\PHPMatcher\Factory\SimpleFactory;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

class FeatureContext extends RestContext {
    const USERS = [
        'admin' => 'test123T',
    ];
    const AUTH_URL = '/api/login_check';
    const AUTH_JSON = '
        {
            "username": "%s",
            "password": "%s"
        } 
    ';
    /**
     * @var AppFixtures
     */
    private $fixtures;

    private $matcher;

    private $manager;

    public function __construct(
        Request $request,
        AppFixtures $fixtures,
        EntityManagerInterface $manager
    ) {
        parent::__construct($request);
        $this->request = $request;
        $this->fixtures = $fixtures;
        $this->matcher = (new SimpleFactory())->createMatcher();
        $this->manager = $manager;
    }

    /**
     * @BeforeScenario @createSchema
     */
    public function createSchema()
    {
        $classes = $this
            ->manager
            ->getMetadataFactory()
            ->getAllMetadata();

        //drop and create schema
        $schemaTool = new SchemaTool($this->manager);
        $schemaTool->dropSchema($classes);
        $schemaTool->createSchema($classes);

        //load fixtures... and execute
        $purger = new ORMPurger($this->manager);
        $fixtureExecutor = new ORMExecutor($this->manager, $purger);

        $fixtureExecutor->execute([
            $this->fixtures,
        ]);
    }

    /**
     * @Given I am authenticated as :user
     */
    public function iAmAuthenticatedAs($user)
    {
        $this->request->setHttpHeader('Content-Type', 'application/ld+json');
        $this->request->send(
            'POST',
            $this->locatePath(self::AUTH_URL),
            [],
            [],
            sprintf(self::AUTH_JSON, $user, self::USERS[$user])
        );

        $json = json_decode($this->request->getContent(), true);

        $this->assertTrue(isset($json['token']));

        $token = $json['token'];
        $this->request->setHttpHeader(
            'Authorization',
            'Bearer '.$token
        );
    }

    /**
     * @Then the JSON matches expected template:
     *
     * @param PyStringNode $json
     */
    public function theJsonMatchesExpectedTemplate(PyStringNode $json)
    {
        $actual = $this->request->getContent();
        $this->assertTrue(
            $this->matcher->match($actual, $json->getRaw())
        );
    }

    /**
     * @BeforeScenario @image
     */
    public function prepareImages()
    {
        copy(
            __DIR__ . '/../fixtures/photo.jpg',
            __DIR__ . '/../fixtures/files/photo.jpg'
        );
    }
}
