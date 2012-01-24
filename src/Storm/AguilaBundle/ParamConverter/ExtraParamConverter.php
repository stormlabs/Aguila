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

        $method = $options['method'];
        $params = array();

        foreach ($options['params'] as $key) {
            if ($request->attributes->has($key)) {
                $params[] = $request->attributes->get($key);
            }
        }

        $repository = $this->registry->getRepository($class, $options['entity_manager']);

        if (false === $object = call_user_func_array(array($repository, $method), $params)) {
            throw new \LogicException('Unable to guess how to get a Doctrine instance from the request information.');
        }

        $request->attributes->set($configuration->getName(), $object);
    }

    public function supports(ConfigurationInterface $configuration)
    {
        if (null === $configuration->getClass()) {
            return false;
        }

        $options = $this->getOptions($configuration);

        if ('' === $options['method']) {
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
            'method' => '',
            'params' => array(),
        ), $configuration->getOptions());
    }
}
