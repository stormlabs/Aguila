<?php
namespace Storm\AguilaBundle\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\DoctrineBundle\Registry;

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Mapping\MappingException;

class ExtraParamConverter implements ParamConverterInterface
{
    protected $registry;

    public function __construct(Registry $registry = null)
    {
        $this->registry = $registry;
    }

    function apply(Request $request, ConfigurationInterface $configuration)
    {
        $class = $configuration->getClass();
        $options = $this->getOptions($configuration);

        // find by criteria
        if (false === $object = $this->findOneBy($class, $request, $options)) {
            throw new \LogicException('Unable to guess how to get a Doctrine instance from the request information.');
        }

        if (null === $object && false === $configuration->isOptional()) {
            throw new NotFoundHttpException(sprintf('%s object not found.', $class));
        }

        $request->attributes->set($configuration->getName(), $object);
    }

    protected function findOneBy($class, Request $request, $options)
    {
        $criteria = array();
        $metadata = $this->registry->getEntityManager($options['entity_manager'])->getClassMetadata($class);

        foreach ($request->attributes->all() as $key => $value) {
            if (isset($options['match'][$key]) && $metadata->hasField($options['match'][$key])) {
                $criteria[$options['match'][$key]] = $value;
            }
        }

        if (!$criteria) {
            return false;
        }

        return $this->registry->getRepository($class, $options['entity_manager'])->findOneBy($criteria);
    }

    public function supports(ConfigurationInterface $configuration)
    {
        if (null === $this->registry) {
            return false;
        }

        if (null === $configuration->getClass()) {
            return false;
        }

        $options = $this->getOptions($configuration);

        if (empty($options['match'])) {
            return false;
        }

        // Doctrine Entity?
        try {
            $this->registry->getEntityManager($options['entity_manager'])->getClassMetadata($configuration->getClass());

            return true;
        } catch (MappingException $e) {
            return false;
        }
    }

    protected function getOptions(ConfigurationInterface $configuration)
    {
        return array_replace(array(
            'entity_manager' => 'default',
            'match' => array(),
        ), $configuration->getOptions());
    }
}
