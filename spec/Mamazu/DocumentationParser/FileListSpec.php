<?php
declare(strict_types=1);

namespace spec\Mamazu\DocumentationParser;

use InvalidArgumentException;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PhpSpec\ObjectBehavior;

class FileListSpec extends ObjectBehavior
{
    /** @var vfsStreamDirectory */
    private $workDir;

    public function let(): void
    {
        $this->workDir = vfsStream::setup('workDir');
    }

    public function it_removes_a_file()
    {
        $this->addFile('abc.md');
        $this->addFile('bcd.md');

        $this->removeFile('abc.md');

        $this->getAllFiles()->shouldIterateAs(['bcd.md']);
    }

    public function it_gets_all_valid_files()
    {
        $this->workDir->addChild(vfsStream::newFile('test.php'));

        $this->addFile('vfs://workDir/test.php');
        $this->addFile('vfs://workDir/bananas.php');

        $this->shouldTrigger(E_USER_WARNING, 'Could not find file: vfs://workDir/bananas.php')
             ->during('getAllValidFiles')
        ;
    }

    public function it_adds_a_file()
    {
        $this->addFile('abc.md');

        $this->getAllFiles()->shouldIterateAs(['abc.md']);
    }

    public function it_throws_an_exception_when_adding_a_directory(): void
    {
        $this->workDir->addChild(vfsStream::newDirectory('abc'));

        $this->shouldThrow(InvalidArgumentException::class)
            ->during('addFile', ['vfs://workDir/abc']);
    }
}
