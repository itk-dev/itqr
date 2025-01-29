<?php

namespace App\Command;

use App\Entity\Url;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\Qr;
use App\Enum\QrModeEnum;

#[AsCommand(
    name: 'app:create-qr',
    description: 'Create a QR Code',
)]
class CreateQrCommand extends Command
{
    public function __construct(
      private EntityManagerInterface $entityManager,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $attributes = $this->setAttributes($input, $output);

        $qr = new Qr();
        $qr->setTitle($attributes['title']);
        $qr->setDepartment($attributes['department']);
        $qr->setDescription($attributes['description']);
        $qr->setAuthor($attributes['author']);
        $qr->setMode(QrModeEnum::{$attributes['mode']});

        $this->entityManager->persist($qr);

        $url = new Url();
        $url->setShortUri('http://localhost/qr/urlFromCommand');
        $url->setUrl($attributes['url']);
        $url->setQr($qr);

        $this->entityManager->persist($url);

        $this->entityManager->flush();

        $io->success($attributes);

        return Command::SUCCESS;
    }

  /**
   * Define attributes from questions.
   *
   * @param $input
   * @param $output
   *
   * @return array
   */
    private function setAttributes($input, $output): array {
      $helper = $this->getHelper('question');
      $attributes = [];

      $question = new Question('Qr title: ');
      $attributes['title'] = $helper->ask($input, $output, $question) ?? 'No title given';

      $question = new Question('URL: ');
      $attributes['url'] = $helper->ask($input, $output, $question) ?? 'https://no-url-given.com';

      $question = new Question('Qr description: ');
      $attributes['description'] = $helper->ask($input, $output, $question) ?? '<strong>No description given</strong>';

      $question = new Question('Qr author: ');
      $attributes['author'] = $helper->ask($input, $output, $question) ?? 'No author given';

      $optionalDepartments = ['Department A', 'Department B', 'Department C'];
      $question = new ChoiceQuestion('Qr department: ', $optionalDepartments, 'Department A');
      $attributes['department'] = $helper->ask($input, $output, $question);

      $optionalModes = QrModeEnum::getAsArray();
      $question = new ChoiceQuestion('Qr mode: ',$optionalModes, QrModeEnum::DEFAULT->name);
      $attributes['mode'] = $helper->ask($input, $output, $question);

      return $attributes;
    }
}
