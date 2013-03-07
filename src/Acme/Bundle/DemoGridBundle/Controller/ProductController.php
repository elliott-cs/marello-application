<?php
namespace Acme\Bundle\DemoGridBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Oro\Bundle\GridBundle\Datagrid\ORM\QueryFactory\QueryFactory;

use Acme\Bundle\DemoGridBundle\Datagrid\UserDatagridManager;

/**
 * @Route("/product")
 */
class ProductController extends Controller
{
    /**
     * @Route("/list.{_format}",
     *      name="acme_demo_gridbundle_product_list",
     *      requirements={"_format"="html|json"},
     *      defaults={"_format" = "html"}
     * )
     */
    public function listAction(Request $request)
    {
        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder
            ->select(
                'm.id AS m_id',
                'm.name AS m_name',
                'p.id',
                'p.name',
                'p.count',
                'p.price',
                'p.description',
                'p.createDate',
                'COUNT(p.id) AS product_count'
            )
            ->from('AcmeDemoBundle:Manufacturer', 'm')
            ->leftJoin('m.products', 'p')
            ->groupBy('m.id');

        /** @var $queryFactory QueryFactory */
        $queryFactory = $this->get('acme_demo_grid.product_query_factory');
        $queryFactory->setQueryBuilder($queryBuilder);

        /** @var $manufacturerGridManager UserDatagridManager */
        $manufacturerGridManager = $this->get('acme_demo_grid.product_grid.manager');
        $datagrid = $manufacturerGridManager->getDatagrid();

        if ('json' == $request->getRequestFormat()) {
            $view = 'OroGridBundle:Datagrid:list.json.php';
        } else {
            $view = 'AcmeDemoGridBundle:User:list.html.twig';
        }
        return $this->render(
            $view,
            array(
                'datagrid' => $datagrid,
                'form'     => $datagrid->getForm()->createView()
            )
        );
    }
}
