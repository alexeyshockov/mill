<?php
namespace Mill\Parser\Annotations;

use Mill\Container;
use Mill\Exceptions\Annotations\InvalidScopeSuppliedException;
use Mill\Parser\Annotation;
use Mill\Parser\Version;

/**
 * Handler for the `@api-scope` annotation.
 *
 */
class ScopeAnnotation extends Annotation
{
    const REQUIRES_VISIBILITY_DECORATOR = false;
    const SUPPORTS_DEPRECATION = false;
    const SUPPORTS_VERSIONING = false;

    /**
     * Name of the scope type that is required for this annotations' method.
     *
     * @var string
     */
    protected $scope;

    /**
     * Description for why this scope is required.
     *
     * @var string|null|false
     */
    protected $description = null;

    /**
     * Return an array of items that should be included in an array representation of this annotation.
     *
     * @var array
     */
    protected $arrayable = [
        'description',
        'scope'
    ];

    /**
     * Parse the annotation out and return an array of data that we can use to then interpret this annotations'
     * representation.
     *
     * @return array
     * @throws InvalidScopeSuppliedException If a supplied scope isn't present in the config file.
     */
    protected function parser()
    {
        $parts = explode(' ', $this->docblock);

        $scope = array_shift($parts);
        $description = trim(implode(' ', $parts));

        if (!empty($scope)) {
            // Validate the supplied scope with what has been configured as allowable.
            $scopes = Container::getConfig()->getScopes();
            if (!in_array($scope, $scopes)) {
                throw InvalidScopeSuppliedException::create($scope, $this->class, $this->method);
            }
        }

        return [
            'scope' => $scope,
            'description' => (!empty($description)) ? $description : null
        ];
    }

    /**
     * Interpret the parsed annotation data and set local variables to build the annotation.
     *
     * To facilitate better error messaging, the order in which items are interpreted here should be match the schema
     * of the annotation.
     *
     * @return void
     */
    protected function interpreter()
    {
        $this->scope = $this->required('scope');
        $this->description = $this->optional('description');
    }

    /**
     * With an array of data that was output from an Annotation, via `toArray()`, hydrate a new Annotation object.
     *
     * @param array $data
     * @param Version|null $version
     * @return self
     */
    public static function hydrate(array $data = [], Version $version = null): self
    {
        /** @var ScopeAnnotation $annotation */
        $annotation = parent::hydrate($data, $version);
        $annotation->setScope($data['scope']);
        $annotation->setDescription($data['description']);

        return $annotation;
    }

    /**
     * Get the name of the scope that this represents.
     *
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Set the name of the scope that this represents.
     *
     * @param string $scope
     * @return self
     */
    public function setScope(string $scope): self
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * Get the description for this scope.
     *
     * @return string|null|false
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the description for this scope.
     *
     * @param string|null|false $description
     * @return self
     */
    public function setDescription($description): self
    {
        $this->description = $description;
        return $this;
    }
}
