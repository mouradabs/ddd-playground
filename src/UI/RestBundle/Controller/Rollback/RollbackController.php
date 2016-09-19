<?php

namespace Leos\UI\RestBundle\Controller\Rollback;

use Leos\UI\RestBundle\Controller\AbstractController;

use Leos\Application\UseCase\Transaction\TransactionCommand;
use Leos\Application\UseCase\Transaction\Request\RollbackDepositDTO;
use Leos\Application\UseCase\Transaction\Request\RollbackWithdrawalDTO;

use Leos\Domain\Payment\Model\RollbackDeposit;
use Leos\Domain\Payment\Model\RollbackWithdrawal;
use Leos\Domain\Transaction\Exception\TransactionNotFoundException;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class RollbackController
 *
 * @package Leos\UI\RestBundle\Controller\Rollback
 *
 * @RouteResource("Rollback", pluralize=false)
 */
class RollbackController extends AbstractController
{
    /**
     * @var TransactionCommand
     */
    private $command;

    /**
     * RollbackController constructor.
     * 
     * @param TransactionCommand $command
     */
    public function __construct(TransactionCommand $command)
    {
        $this->command = $command;
    }
    
    /**
     * @ApiDoc(
     *     resource = true,
     *     section="Rollback",
     *     description = "Rollback the given deposit",
     *     statusCodes = {
     *       200 = "Returned when successful",
     *       400 = "Returned when Bad request",
     *       404 = "Returned when not found"
     *     }
     * )
     *
     * @RequestParam(name="deposit", description="Deposit identifier")
     *
     * @View(statusCode=202, serializerGroups={"Identifier", "Basic"})
     *
     * @param ParamFetcher $fetcher
     * 
     * @return RollbackDeposit
     */
    public function postDepositAction(ParamFetcher $fetcher): RollbackDeposit
    {
        try {

            $dto = new RollbackDepositDTO($fetcher->get('deposit'));

            return $this->command->rollbackDeposit($dto);

        } catch (TransactionNotFoundException $e) {

            throw new NotFoundHttpException($e->getMessage(), $e, $e->getCode());

        } catch (\InvalidArgumentException $e) {

            throw new BadRequestHttpException($e->getMessage(), $e, $e->getCode());
        }
    }

    /**
     * @ApiDoc(
     *     resource = true,
     *     section="Rollback",
     *     description = "Rollback the given withdrawal",
     *     statusCodes = {
     *       200 = "Returned when successful",
     *       400 = "Returned when Bad request",
     *       404 = "Returned when not found"
     *     }
     * )
     *
     * @RequestParam(name="withdrawal", description="Withdrawal identifier")
     *
     * @View(statusCode=202, serializerGroups={"Identifier", "Basic"})
     *
     * @param ParamFetcher $fetcher
     *
     * @return RollbackWithdrawal
     */
    public function postWithdrawalAction(ParamFetcher $fetcher): RollbackWithdrawal
    {
        try{

            $dto = new RollbackWithdrawalDTO($fetcher->get('withdrawal'));

            return $this->command->rollbackWithdrawal($dto);

        } catch (TransactionNotFoundException $e) {

            throw new NotFoundHttpException($e->getMessage(), $e, $e->getCode());

        } catch (\InvalidArgumentException $e) {

            throw new BadRequestHttpException($e->getMessage(), $e, $e->getCode());

        }
    }
}
