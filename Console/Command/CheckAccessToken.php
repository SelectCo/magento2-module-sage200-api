<?php
declare(strict_types=1);

namespace SelectCo\Sage200Api\Console\Command;

use SelectCo\Sage200Api\Helper\Data;
use SelectCo\Sage200Api\Model\Token\AccessToken;
use SelectCo\Sage200Api\Model\Token\RefreshToken;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckAccessToken extends Command
{
    /**
     * @var AccessToken
     */
    private $accessToken;
    /**
     * @var RefreshToken
     */
    private $refreshToken;
    /**
     * @var Data
     */
    private $helper;

    public function __construct(
        AccessToken $accessToken,
        RefreshToken $refreshToken,
        Data $helper
    ) {
        parent::__construct();
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->helper = $helper;
    }

    /**
     * Initialization of the command.
     */
    protected function configure()
    {
        $this->setName('sage200:token:check');
        $this->setDescription('Check if access token is still valid.');
        parent::configure();
    }

    /**
     * CLI command description.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $exitCode = 0;
        if (!$this->helper->isModuleEnabled()) {
            $output->writeln('<error>Module is not enabled!</error>');
            return $exitCode;
        }

        if (($token = $this->accessToken->checkAccessTokenExpiry())) {
            $output->writeln(
                "<info>Access token expires in {$token['hoursToExpire']} hours, at {$token['expiryDate']} </info>"
            );
        } else {
            $output->writeln('<error>Access token has expired!</error>');
            $exitCode = 1;
        }

        if (($token = $this->refreshToken->checkRefreshTokenExpiry())) {
            $output->writeln(
                "<info>Refresh token expires in {$token['daysToExpire']} days, at {$token['expiryDate']} </info>"
            );
        } else {
            $output->writeln('<error>Refresh token has expired!</error>');
            $exitCode = 1;
        }

        return $exitCode;
    }
}
