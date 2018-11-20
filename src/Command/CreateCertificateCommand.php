<?php

namespace App\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateCertificateCommand extends ContainerAwareCommand {

    private static $keyFile = 'idp.key';
    private static $certFile = 'idp.crt';

    private static $certDirectory = 'certs';

    public function configure() {
        $this->setName('app:create-certificate');
    }

    public function run(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);

        $keyFile = sprintf('%s/%s/%s', $this->getContainer()->getParameter('kernel.project_dir'), static::$certDirectory, static::$keyFile);
        $certFile = sprintf('%s/%s/%s', $this->getContainer()->getParameter('kernel.project_dir'), static::$certDirectory, static::$certFile);

        $config = [
            'digest_alg' => 'sha512',
            'private_key_bits' => 4096,
            'private_key_type' => OPENSSL_KEYTYPE_RSA
        ];

        $privKey = openssl_pkey_new($config);
        $data = [ ];

        $data['countryName'] = $io->ask('countryName', 'DE');
        $data['stateOrProvinceName'] = $io->ask('stateOrProvinceName', 'Nordrhein-Westfalen');
        $data['localityName'] = $io->ask('localityName', 'Aachen');
        $data['organizationName'] = $io->ask('organizationName', 'SchoolIT');
        $data['organizationalUnitName'] = $io->ask('organizationalUnitName', 'SchoolIT Development');
        $data['commonName'] = $io->ask('commonName', 'sso.school.it');
        $data['emailAddress'] = $io->ask('emailAddress', 'admin@school.it');

        $csr = openssl_csr_new($data, $privKey, $config);
        $cert = openssl_csr_sign($csr, null, $privKey, 10*365, $config);

        openssl_x509_export($cert, $certout);
        openssl_pkey_export($privKey, $keyout);

        file_put_contents($keyFile, $keyout);
        file_put_contents($certFile, $certout);

        $io->success('Certificate generated successfully');
    }
}