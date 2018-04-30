<?php

namespace Dhii\Config\FuncTest;

use Dhii\Config\DereferencingConfigMapFactory as TestSubject;
use Psr\Container\ContainerInterface;
use stdClass;
use Traversable;
use Xpmock\TestCase;
use Exception as RootException;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_MockObject_MockBuilder as MockBuilder;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class DereferencingConfigMapFactoryTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Config\DereferencingConfigMapFactory';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @param array $methods The methods to mock.
     *
     * @return MockObject|TestSubject The new instance.
     */
    public function createInstance($methods = [], $constructorArgs = [])
    {
        is_array($methods) && $methods = $this->mergeValues($methods, [
        ]);

        $mock = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
            ->setMethods($methods)
            ->setConstructorArgs($constructorArgs)
            ->getMock();

        return $mock;
    }

    /**
     * Merges the values of two arrays.
     *
     * The resulting product will be a numeric array where the values of both inputs are present, without duplicates.
     *
     * @since [*next-version*]
     *
     * @param array $destination The base array.
     * @param array $source      The array with more keys.
     *
     * @return array The array which contains unique values
     */
    public function mergeValues($destination, $source)
    {
        return array_keys(array_merge(array_flip($destination), array_flip($source)));
    }

    /**
     * Creates a mock that both extends a class and implements interfaces.
     *
     * This is particularly useful for cases where the mock is based on an
     * internal class, such as in the case with exceptions. Helps to avoid
     * writing hard-coded stubs.
     *
     * @since [*next-version*]
     *
     * @param string   $className      Name of the class for the mock to extend.
     * @param string[] $interfaceNames Names of the interfaces for the mock to implement.
     *
     * @return MockBuilder The builder for a mock of an object that extends and implements
     *                     the specified class and interfaces.
     */
    public function mockClassAndInterfaces($className, $interfaceNames = [])
    {
        $paddingClassName = uniqid($className);
        $definition = vsprintf('abstract class %1$s extends %2$s implements %3$s {}', [
            $paddingClassName,
            $className,
            implode(', ', $interfaceNames),
        ]);
        eval($definition);

        return $this->getMockBuilder($paddingClassName);
    }

    /**
     * Creates a mock that uses traits.
     *
     * This is particularly useful for testing integration between multiple traits.
     *
     * @since [*next-version*]
     *
     * @param string[] $traitNames Names of the traits for the mock to use.
     *
     * @return MockBuilder The builder for a mock of an object that uses the traits.
     */
    public function mockTraits($traitNames = [])
    {
        $paddingClassName = uniqid('Traits');
        $definition = vsprintf('abstract class %1$s {%2$s}', [
            $paddingClassName,
            implode(
                ' ',
                array_map(
                    function ($v) {
                        return vsprintf('use %1$s;', [$v]);
                    },
                    $traitNames)),
        ]);
        var_dump($definition);
        eval($definition);

        return $this->getMockBuilder($paddingClassName);
    }

    /**
     * Creates a new exception.
     *
     * @since [*next-version*]
     *
     * @param string $message The exception message.
     *
     * @return RootException|MockObject The new exception.
     */
    public function createException($message = '')
    {
        $mock = $this->getMockBuilder('Exception')
            ->setConstructorArgs([$message])
            ->getMock();

        return $mock;
    }

    /**
     * Creates a new Container instance.
     *
     * @since [*next-version*]
     *
     * @param array $methods]
     *
     * @return MockObject|ContainerInterface The new container.
     */
    public function createContainer($methods = [])
    {
        is_array($methods) && $methods = $this->mergeValues($methods, [
        ]);

        $mock = $this->getMockBuilder('Psr\Container\ContainerInterface')
            ->setMethods($methods)
            ->getMock();

        return $mock;
    }

    /**
     * Tests whether a valid instance of the test subject can be created.
     *
     * @since [*next-version*]
     */
    public function testCanBeCreated()
    {
        $subject = $this->createInstance(null);

        $this->assertInstanceOf(static::TEST_SUBJECT_CLASSNAME, $subject, 'A valid instance of the test subject could not be created.');
        $this->assertInstanceOf('Dhii\Config\ConfigFactoryInterface', $subject, 'Subject does not implement required interface');
    }

    /**
     * Tests that `make()` works as expected.
     *
     * @since [*next-version*]
     */
    public function testMake()
    {
        $ref1 = 'balance/anton';
        $ref2 = 'balance/miguel';
        $val1 = rand(100000, 1000000) / 100;
        $val2 = rand(100000, 1000000) / 100;
        $referenceData = [
            $ref1         => $val1,
            $ref2         => $val2,
        ];
        $container = $this->createContainer(['get', 'has']);
        $container->expects($this->any())
            ->method('has')
            ->will($this->returnValueMap([
                [$ref1, true],
                [$ref2, true],
            ]));
        $container->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap([
                [$ref1, $referenceData[$ref1]],
                [$ref2, $referenceData[$ref2]],
            ]));

        $data = [
            [
                'name' => 'Anton',
                'surname' => 'Ukhanev',
                'props' => [
                    'balance' => sprintf('${%1$s}', $ref1),
                    'phone' => 'iPhone',
                    'own_place' => true,
                ],
            ],
            [
                'name' => 'Miguel',
                'surname' => 'Muscat',
                'props' => [
                    'balance' => sprintf('${%1$s}', $ref2),
                    'phone' => 'Samsung',
                    'own_place' => false,
                ],
            ],
        ];

        $subject = $this->createInstance(null, [$container]);
        $_subject = $this->reflect($subject);

        $expected = $data;
        $expected[0]['props']['balance'] = $val1;
        $expected[1]['props']['balance'] = $val2;
        $result = $subject->make((object) [TestSubject::K_DATA => $data]);
        echo $this->_debugIterableRecursive($result);
        $normalizedResult = $this->_iterableToArrayRecursive($result);
        $this->assertEquals($expected, $normalizedResult, 'Config hierarchy has wrong structure');

    }

    /**
     * Converts a hierarchy of iterables to an array.
     *
     * @since [*next-version*]
     *
     * @param array|stdClass|Traversable $iterable The iterable to convert.
     *
     * @return array The array with the same structure as the iterable hierarchy.
     */
    protected function _iterableToArrayRecursive($iterable)
    {
        $result = [];
        foreach ($iterable as $_key => $_value) {
            $value = (is_array($_value)
                || ($_value instanceof Traversable)
                || ($_value instanceof stdClass))
                ? $this->_iterableToArrayRecursive($_value)
                : $_value;
            $result[$_key] = $value;
        }

        return $result;
    }

    /**
     */
    protected function _debugIterableRecursive($iterable, $level = 0)
    {
        $indent = "\t";
        $result = '';

        $type = is_object($iterable) ? get_class($iterable) : gettype($iterable);
        $result .= sprintf('%1$s:', $type) . "\n";

        foreach ($iterable as $_key => $_value) {
            $isIterable = (is_array($_value)
                || ($_value instanceof Traversable)
                || ($_value instanceof stdClass));
            $_type = is_object($_value) ? get_class($_value) : gettype($_value);
            $_indent = str_repeat($indent, $level + 1);
            $value = $isIterable
                ? $this->_debugIterableRecursive($_value, $level + 1)
                : $_value;
            $result .= $_indent . $_key . ' => ' . (is_scalar($_value) ? (sprintf('(%1$s) ', $_type) . $value) : ($isIterable ? $value : $_type)) . "\n";
        }

        return $result;
    }
}
