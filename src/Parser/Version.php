<?php
namespace Mill\Parser;

use Composer\Semver\Constraint\ConstraintInterface;
use Composer\Semver\Constraint\MultiConstraint;
use Composer\Semver\Semver;
use Composer\Semver\VersionParser;
use Mill\Exceptions\Version\UnrecognizedSchemaException;

/**
 * Version parsing class for realizing a `@api-version` annotation.
 *
 */
class Version
{
    /**
     * The parsed semver constraint object.
     *
     * @var ConstraintInterface
     */
    protected $constraint;

    /**
     * Name of the class that this version is within.
     *
     * @var string
     */
    protected $class;

    /**
     * Name of the method that this version is within.
     *
     * @var string
     */
    protected $method;

    /**
     * @param string $constraint
     * @param string $class
     * @param string $method
     * @throws UnrecognizedSchemaException If an `@api-version` annotation was found with an unrecognized schema.
     */
    public function __construct($constraint, $class, $method)
    {
        $this->class = $class;
        $this->method = $method;

        try {
            $parser = new VersionParser;
            $this->constraint = $parser->parseConstraints($constraint);
        } catch (\UnexpectedValueException $e) {
            throw UnrecognizedSchemaException::create($constraint, $this->class, $this->method);
        }
    }

    /**
     * Assert that a given version string matches the current parsed range.
     *
     * @param string $version
     * @return bool
     */
    public function matches($version)
    {
        return Semver::satisfies($version, $this->getConstraint());
    }

    /**
     * @return string
     */
    public function getConstraint()
    {
        return $this->constraint->getPrettyString();
    }

    /**
     * Is the parsed version constraint a range (i.e. a multi constraint)?
     *
     * @return bool
     */
    public function isRange()
    {
        return $this->constraint instanceof MultiConstraint;
    }
}
