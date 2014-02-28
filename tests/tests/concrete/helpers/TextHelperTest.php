<?php
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-06-13 at 17:46:42.
 */
class TextHelperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TextHelper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = Loader::helper('text');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testAsciify() 
    {
        $this->assertEquals("Mixed with English and Germaen", $this->object->asciify("Mixed with English and Germän", "de_DE"));
        $this->assertEquals("Mixed with English and ", $this->object->asciify("Mixed with English and 日本人"));
        $this->assertEquals("Mixed with English and .doc", $this->object->asciify("Mixed with English and 日本人.doc"));
        $this->assertEquals("Mixed with English and .", $this->object->asciify("Mixed with English and 日本人.日本人"));
        $this->assertEquals("", $this->object->asciify("日本人"));
        $this->assertEquals(".doc", $this->object->asciify("日本人.doc"));
        $this->assertEquals(".", $this->object->asciify("日本人.日本人"));
    }

    public function testUrlify() 
    {
        $this->assertEquals("simple-test-case", $this->object->urlify("This is a simple test case"));
        $this->assertEquals ('jetudie-le-francais', $this->object->urlify(' J\'étudie le français '));
        $this->assertEquals ('lo-siento-no-hablo-espanol', $this->object->urlify('Lo siento, no hablo español.'));
        $this->assertEquals ('f3pws', $this->object->urlify('ΦΞΠΏΣ'));
        $this->assertEquals ('yo-hablo-espanol', $this->object->urlify('¿Yo hablo español?'));
        
        // Test for many rounds with a language, that has no map associated
        // This causes a "regular expression is too large" error on old versions
        for ($i = 0; $i < 1000; $i++) {
            $this->object->urlify('Lo siento, no hablo español.',60,-1);
        }
    }

    public function testShortenTextWord() 
    {
        $this->assertEquals("This is a simple test...", $this->object->shortenTextWord("This is a simple test case",24,"..."));
        $this->assertEquals("This is a simple test etc", $this->object->shortenTextWord("This is a simple test case",22," etc"));
        $this->assertEquals("This is a simple test.", $this->object->shortenTextWord("This is a simple test case",21,"."));
        $this->assertEquals("The quick brown fox jumps over the lazy dog", $this->object->shortenTextWord("The quick brown fox jumps over the lazy dog"));
        $this->assertEquals("The lazy fox jumps over the quick brown dog", $this->object->shortenTextWord("The lazy fox jumps over the quick brown dog",0));
        $this->assertEquals("This_is_a_simple_test_ca…", $this->object->shortenTextWord("This_is_a_simple_test_case",24,"…"));
    }
    
    public function testWordSafeShortText() 
    {
        $this->assertEquals("This is a simple test...", $this->object->wordSafeShortText("This is a simple test case",24,"..."));
        $this->assertEquals("This is a simple test etc", $this->object->wordSafeShortText("This is a simple test case",22," etc"));
        $this->assertEquals("This is a simple test.", $this->object->wordSafeShortText("This is a simple test case",21,"."));
        $this->assertEquals("The quick brown fox jumps over the lazy dog", $this->object->wordSafeShortText("The quick brown fox jumps over the lazy dog"));
        $this->assertEquals("The lazy fox jumps over the quick brown dog", $this->object->wordSafeShortText("The lazy fox jumps over the quick brown dog",0));
        $this->assertEquals("This_is_a_simple_test_ca…", $this->object->wordSafeShortText("This_is_a_simple_test_case",24,"…"));
    }
   
    public function testAutolink() {
        $this->assertEquals('<a href="https://www.google.com" rel="nofollow">https://www.google.com</a>', Loader::helper('text')->autolink('https://www.google.com'));
        $this->assertEquals('<a href="http://www.google.com" rel="nofollow">http://www.google.com</a>', Loader::helper('text')->autolink('http://www.google.com'));
        $this->assertEquals('<a href="https://www.google.com" target="_blank" rel="nofollow">https://www.google.com</a>', Loader::helper('text')->autolink('https://www.google.com', true));
        $this->assertEquals('<a href="http://www.google.com" target="_blank" rel="nofollow">http://www.google.com</a>', Loader::helper('text')->autolink('http://www.google.com', true));
        $this->assertEquals('<a href="http://www.google.com" target="_blank" rel="nofollow">http://www.google.com</a>', Loader::helper('text')->autolink('www.google.com', true));
        $this->assertEquals('<a href="http://www.google.com" rel="nofollow">http://www.google.com</a>', Loader::helper('text')->autolink('www.google.com'));
        $this->assertEquals('<a href="http://www.google.com" target="_blank">http://www.google.com</a>', Loader::helper('text')->autolink('http://www.google.com', true, false));
        $this->assertEquals('<a href="http://www.google.com">http://www.google.com</a>', Loader::helper('text')->autolink('http://www.google.com', false, false));
    }

}
