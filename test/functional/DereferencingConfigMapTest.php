<?php

namespace Dhii\Config\FuncTest;

use Dhii\Config\DereferencingConfigMap as TestSubject;
use Dhii\Config\ConfigInterface;
use Psr\Container\ContainerInterface;
use Xpmock\TestCase;
use Exception as RootException;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_MockObject_MockBuilder as MockBuilder;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class DereferencingConfigMapTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Config\DereferencingConfigMap';

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
     * Creates a new Container.
     *
     * @since [*next-version*]
     *
     * @param array|null $methods The methods to mock.
     *
     * @return MockObject|ContainerInterface The new Container instance.
     */
    public function createContainer($methods = [])
    {
        is_array($methods) && $methods = $this->mergeValues($methods, [
            'has',
            'get'
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
        $subject = $this->createInstance(null, [[]]);

        $this->assertInstanceOf(
            'Dhii\Data\Container\ContainerInterface',
            $subject,
            'A valid instance of the test subject could not be created.'
        );
    }

    /**
     * Tests that `get()` works correctly when retrieving values 2 levels deep.
     *
     * @since [*next-version*]
     */
    public function testGet2Levels()
    {
        $separator = ConfigInterface::KEY_SEPARATOR;
        $key1 = uniqid('key');
        $key2 = uniqid('key');
        $val2 = uniqid('val');
        $val1 = (object) [$key2 => $val2];
        $data = (object) [
            $key1 => $val1,
        ];
        $subject = $this->createInstance(null, [$data, null]);
        $_subject = $this->reflect($subject);

        $result = $subject->get($key1);
        $this->assertEquals($val1, $result, 'Wrong result retrieved for level 1');

        $path = implode($separator, [$key1, $key2]);
        $result2 = $subject->get($path);
        $this->assertEquals($val2, $result2, 'Wrong result retrieved for level 2');
    }

    /**
     * Tests that `get()` works correctly when retrieving values 2 levels deep.
     *
     * @since [*next-version*]
     */
    public function testGetDereference()
    {
        $separator = ConfigInterface::KEY_SEPARATOR;
        $key1 = uniqid('key');
        $ref = uniqid('ref');
        $refVal = uniqid('val');
        $token = TestSubject::REF_TOKEN_START . $ref . TestSubject::REF_TOKEN_END;
        $seed = uniqid('val');
        $val1 = $token . $seed;
        $container = $this->createContainer();
        $data = (object) [
            $key1 => $val1,
        ];
        $subject = $this->createInstance(null, [$data, $container]);
        $_subject = $this->reflect($subject);

        $container->expects($this->exactly(1))
            ->method('get')
            ->with($ref)
            ->will($this->returnValue($refVal));

        $result = $subject->get($key1);
        $this->assertEquals($refVal . $seed, $result, 'Wrong result retrieved for value with reference');
    }

    /**
     * Tests that iterating over the test subject will de-reference tokens in values.
     *
     * @since [*next-version*]
     */
    public function testIterateDereference()
    {
        $key1 = uniqid('key');
        $key2 = uniqid('key');
        $ref = uniqid('ref');
        $refVal = uniqid('refval');
        $token = TestSubject::REF_TOKEN_START . $ref . TestSubject::REF_TOKEN_END;
        $seed1 = uniqid('seed');
        $seed2 = uniqid('seed');
        $val1 = $token . $seed1;
        $val2 = $token . $seed2;
        $data = (object) [
            $key1  => $val1,
            $key2  => $val2,
        ];
        $container = $this->createContainer();
        $subject = $this->createInstance(null, [$data, $container]);
        $_subject = $this->reflect($subject);

        $container->expects($this->exactly(2))
            ->method('get')
            ->with($ref)
            ->will($this->returnValue($refVal));

        $expected = [
            $key1   => $refVal . $seed1,
            $key2   => $refVal . $seed2,
        ];
        $result = iterator_to_array($subject);
        $this->assertEquals($expected, $result, 'Dereferencing iteration produced wrong result');
    }
}
