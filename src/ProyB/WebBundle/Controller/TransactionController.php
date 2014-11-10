<?php

namespace ProyB\WebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use ProyB\DomainModelBundle\Entity\Transaction;
use ProyB\DomainModelBundle\Form\TransactionType;
use ProyB\DomainModelBundle\Form\AdminTransactionType;

class TransactionController extends Controller
{
    private function getValidations(){
        $validations=[];
        $validator=$this->get("validator");
        $metadata=$validator->getMetadataFor(new Transaction());
        $constrainedProperties=$metadata->getConstrainedProperties();
        foreach($constrainedProperties as $constrainedProperty)
        {
            $propertyMetadata=$metadata->getPropertyMetadata($constrainedProperty);
            $constraints=$propertyMetadata[0]->constraints;
            $outputConstraintsCollection=[];
            foreach($constraints as $constraint)
            {
                $class = new \ReflectionObject($constraint);
                $constraintName=$class->getShortName();
                $constraintParameter=null;
                switch ($constraintName) 
                {
                    case "NotBlank":
                        $param="notBlank";
                        break;
                    case "Type":
                        $param=$constraint->type;
                        break;
                    case "Length":
                        $param=$constraint->max;
                        break;
                }
                $outputConstraintsCollection[$constraintName]=$param;
            }
            $validations[$constrainedProperty]=$outputConstraintsCollection;
        }
        return $validations;
    }

    //Ajax functions
    public function loadMoreAction(Request $request){
        $isAjax = $this->getRequest()->isXmlHttpRequest();
        $env = $this->container->get( 'kernel' )->getEnvironment(); 
        $lastrow = 0;
        $unfiltered = false;
        
        if ($isAjax === false){
            if ($env === 'prod'){
                throw new AccessDeniedException(); //Security: Only allow GET METHOD ON DEV
            }
            $lastrow = $request->query->get('lastrow');
            $unfiltered = ( $request->query->get('unfiltered') == '1') ? true : false;
        }else{
            $lastrow = $request->request->get('lastrow');
            $unfiltered = $request->request->get('unfiltered');
        }
        
        $rpp = $this->container->getParameter('rows_per_page');
        $response = new JsonResponse();
        $em = $this->getDoctrine()->getManager();
        if (($unfiltered === false) && ($this->get('security.context')->isGranted('ROLE_ADMIN'))){
            $entities = $em->getRepository('ProyBDomainModelBundle:Transaction')->findAllPaginated($lastrow,$rpp);//todas 
        }else{
            $entities = $em->getRepository('ProyBDomainModelBundle:Transaction')->findActiveForUserPaginated($lastrow,$rpp,$this->getUser());//activas
        }
        $arrEntities = array();
        $i = 0;
        foreach ($entities as $entity){
            $arrEntities[$i] = array("id" => $entity->getId(),
                                    "state" => $entity->getState()->getDescription(), 
                                    "date" => date_format($entity->getDate(), 'm/d/y'), 
                                    "amount" => $entity->getAmount(),
                                    "comment" => $entity->getComment(),
                                    "inactiveDate" => (($entity->getInactiveDate() === null) ? "-" : date_format($entity->getInactiveDate(), 'm/d/y H:i:s')),
                                    "edit_path" => (($this->get('security.context')->isGranted('ROLE_ADMIN')) ? $this->generateUrl('admin_transaction_edit', array('id' => $entity->getId())):$this->generateUrl('transaction_edit', array('id' => $entity->getId()))),
                                    "show_path" => (($this->get('security.context')->isGranted('ROLE_ADMIN')) ? $this->generateUrl('admin_transaction_show', array('id' => $entity->getId())):$this->generateUrl('transaction_show', array('id' => $entity->getId()))),
                                    "new_path" => (($this->get('security.context')->isGranted('ROLE_ADMIN')) ? $this->generateUrl('admin_transaction_new_from', array('id' => $entity->getId())):$this->generateUrl('transaction_new_from', array('id' => $entity->getId()))),
                                    );
            $i++;
        }
        $response->setData(array("success" => true,
                                "data" => $arrEntities,
                                "lastrow" => $lastrow+count($entities),
                                "eof" => ((count($entities) < $rpp) ? true : false),
                                )
                          );
        return $response; 
    }
    
    public function countStatesAction(Request $request){
        $isAjax = $this->getRequest()->isXmlHttpRequest();
        $env = $this->container->get('kernel')->getEnvironment(); 
        $idTrx = null;
        $idState = null;
        
        if ($isAjax === false){
            if ($env === 'prod'){
                throw new AccessDeniedException(); //Security: Only allow GET METHOD ON DEV
            }
            $idTrx = $request->query->get('idTrx');
            $idState = $request->query->get('idState');
        }else{
            $idTrx = $request->request->get('idTrx');
            $idState = $request->request->get('idState');
        }
        $response = new JsonResponse();
        $em = $this->getDoctrine()->getManager();
        $state = $em->getRepository('ProyBDomainModelBundle:State')->find($idState);
        $count = $em->getRepository('ProyBDomainModelBundle:Transaction')->selectCountByState($state);
//        //Si es el STATE original no suma 1
//        if (!empty($idTrx)){
//            $trx = $em->getRepository('ProyBDomainModelBundle:Transaction')->find($idTrx);
//            if ($trx->getState()->getId()<>$state->getId()){
//                $count++;
//            }
//        }else{
//            $count++; //si es nueva se suma 1 a la cantidad
//        }
        $response->setData(array("success" => true,
                                "count" => $count,
                                )
                          );
        return $response; 
    }
    
    //Normal functions
    private function getUrlShow($entity){
        //Si es administrador  
        if (true === $this->get('security.context')->isGranted('ROLE_ADMIN')){
            $url = $this->generateUrl('admin_transaction_show', array('id' => $entity->getId()));
        }
        else{
           $url = $this->generateUrl('transaction_show', array('id' => $entity->getId()));
        }
        
        return $url;
    }
    
    private function checkSecurity($entity){
        //If not ADMIN
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')){ 
            //If TRX's inactive or USER doesn't have TRX's STATE
            if ( (!is_null($entity->getInactiveDate())) || (!in_array($entity->getState(), $this->getUser()->getStates()->getValues())) ){
                throw new AccessDeniedException();
            }
        }
    }
    
    private function setUpdateFields($entity){
        //Date
        $entity->setUpdateDate(new \DateTime());
        //User
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')){ 
            $entity->setUpdateUser($this->getUser());
        }
    }
        
    private function setInsertFields($entity){
        //Date
        $entity->setInsertDate(new \DateTime());
        //User
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')){ 
            $entity->setInsertUser($this->getUser());
        }
        $this->setUpdateFields($entity);
    }

    private function setInactiveFields($entity){
        //Date
        $entity->setInactiveDate(new \DateTime());
        //User
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')){ 
            $entity->setInsertUser($this->getUser());
        }
    }
    
    public function indexAction(){
        //Si es ADMIN
        if (true === $this->get('security.context')->isGranted('ROLE_ADMIN')){
            $paths = array ('show' => 'admin_transaction_show',
                            'edit' => 'admin_transaction_edit',
                            'newFrom' => 'admin_transaction_new_from',
                            'new' => 'admin_transaction_new',);
        }
        //si es USER
        else{
            $paths = array ('show' => 'transaction_show',
                            'edit' => 'transaction_edit',
                            'newFrom' => 'transaction_new_from',
                            'new' => 'transaction_new',);
        }
        $request = $this->container->get('request');
        $routeName = $request->get('_route');
        $rpp = $this->container->getParameter('rows_per_page');
        $em = $this->getDoctrine()->getManager();
        if ($routeName == 'admin_transactions'){
            $entities = $em->getRepository('ProyBDomainModelBundle:Transaction')->findAllPaginated(0,$rpp); //All
            $paths['unfiltered'] = true;
        }else{
            $entities = $em->getRepository('ProyBDomainModelBundle:Transaction')->findActiveForUserPaginated(0,$rpp,$this->getUser()); //Active
            $paths['unfiltered'] = false;
        }
        $count = count($entities);
        $inactiveSetForm = $this->createInactiveSetForm();
        
        $env = $this->container->get('kernel')->getEnvironment();
        
        return $this->render('ProyBWebBundle:Transaction:index.html.twig', array(
            'entities' => $entities,
            'inactive_set_form' => $inactiveSetForm->createView(),
            'count' => $count,
            'paths'=> $paths,
            'ajax_path' => (($env === 'prod')?'/ajax/':'/app_dev.php/ajax/'),
        ));
    }

    public function newAction($id = null){
        $entityNew = new Transaction();
        $count = null;
        if (!is_null($id)){
            $em = $this->getDoctrine()->getManager();
            $entityOld = $em->getRepository('ProyBDomainModelBundle:Transaction')->find($id);
            if (!$entityOld) {
                throw $this->createNotFoundException('Unable to find Transaction entity.');
            }
            $this->checkSecurity($entityOld);
            $entityNew->setState($entityOld->getState());
            $entityNew->setDate($entityOld->getDate());
            $entityNew->setAmount($entityOld->getAmount());
            $entityNew->setComment($entityOld->getComment());
            
            $count = $em->getRepository('ProyBDomainModelBundle:Transaction')->selectCountByState($entityOld->getState());
            $count ++;
        }
        
        $form   = $this->createCreateForm($entityNew);
        $form->get('count')->setData($count);
        
        $env = $this->container->get('kernel')->getEnvironment();
        
        return $this->render('ProyBWebBundle:Transaction:new.html.twig', array('entity' => $entityNew,
                                                                                'form'   => $form->createView(),
                                                                                'ajax_path' => (($env === 'prod')?'/ajax/':'/app_dev.php/ajax/')
                                                                            )
                            );
    }
    
    public function createAction(Request $request){
        $entity = new Transaction();
        $this->setInsertFields($entity);//TambiÃ©n llama a setUpdateFields
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('transactions'));
        }

        return $this->render('ProyBWebBundle:Transaction:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    
    private function createCreateForm(Transaction $entity){
        //Si es administrador mostrar todos los States y el FormType correspondiente
        if (true === $this->get('security.context')->isGranted('ROLE_ADMIN')){
            $em = $this->getDoctrine()->getManager();
            $states = $em->getRepository('ProyBDomainModelBundle:State')->findall();
            $formType = new AdminTransactionType($states);
            $urlCreate = $this->generateUrl('admin_transaction_create');
        }else{
            $states = $this->getUser()->getStates();
            $formType = new TransactionType($states);
            $urlCreate = $this->generateUrl('transaction_create');
        }
        $form = $this->createForm($formType, $entity, array(
            'action' => $urlCreate,
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    public function showAction($id){
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ProyBDomainModelBundle:Transaction')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Transaction entity.');
        }
        
        $this->checkSecurity($entity);
        
        $deleteForm = $this->createDeleteForm($id);
        $inactiveForm = $this->createInactiveForm($id);
        
        $count = $em->getRepository('ProyBDomainModelBundle:Transaction')->selectCountByState($entity->getState());
        
        if (true === $this->get('security.context')->isGranted('ROLE_ADMIN')){
          $pathEdit = $this->generateUrl('admin_transaction_edit', array('id' => $entity->getId()));  
        }else{
            $pathEdit = $this->generateUrl('transaction_edit', array('id' => $entity->getId()));
        }
        return $this->render('ProyBWebBundle:Transaction:show.html.twig', array(
            'entity'        => $entity,
            'delete_form'   => $deleteForm->createView(),
            'inactive_form' => $inactiveForm->createView(),
            'count'         => $count,
            'pathEdit'       => $pathEdit,
        ));
    }

    public function editAction($id){
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ProyBDomainModelBundle:Transaction')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Transaction entity.');
        }

        $this->checkSecurity($entity);
        
        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);
        $inactiveForm = $this->createInactiveForm($id);
        
        $count = $em->getRepository('ProyBDomainModelBundle:Transaction')->selectCountByState($entity->getState());
        $editForm->get('count')->setData($count);
        
        $env = $this->container->get('kernel')->getEnvironment();
        
        return $this->render('ProyBWebBundle:Transaction:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'inactive_form' => $inactiveForm->createView(),
            'ajax_path' => (in_array($env, array('test', 'dev'))?'/app_dev.php/ajax/':'/ajax/')
        ));
    }
    
    private function createEditForm(Transaction $entity){
        //Si es administrador mostrar todos los States
        if (true === $this->get('security.context')->isGranted('ROLE_ADMIN')){
            $em = $this->getDoctrine()->getManager();
            $states = $em->getRepository('ProyBDomainModelBundle:State')->findAll();
            $formType = new AdminTransactionType($states);
        }
        else{
            $states = $this->getUser()->getStates();
            $formType = new TransactionType($states->getValues());
        }
       $form = $this->createForm($formType, $entity, array(
            'action' => $this->generateUrl('transaction_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    public function updateAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ProyBDomainModelBundle:Transaction')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Transaction entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $inactiveForm = $this->createInactiveForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            //TIP
            //$data = $editForm->getData(); //Devuelve un objeto de clase Transaction
            //$fecha = $editForm["date"]->getData(); //Devuelve el  valor de un campo del formulario
            $this->setUpdateFields($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('transactions'));
        }

        $env = $this->container->get('kernel')->getEnvironment();
        
        return $this->render('ProyBWebBundle:Transaction:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'inactive_form'=> $inactiveForm->createView(),
            'ajax_path' => (($env === 'prod')?'/ajax/':'/app_dev.php/ajax/'),
        ));
    }

    public function deleteAction(Request $request, $id){
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ProyBDomainModelBundle:Transaction')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Transaction entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('transactions'));
    }

    private function createDeleteForm($id){
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('transaction_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
  
    public function inactiveAction(Request $request, $id){
        $form = $this->createInactiveForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ProyBDomainModelBundle:Transaction')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Transaction entity.');
            }
            $this->setInactiveFields($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('transactions'));
    }
    
    private function createInactiveForm($id){
        return $this->get('form.factory')->createNamedBuilder('form')
            ->setAction($this->generateUrl('transaction_inactive', array('id' => $id)))
            ->setMethod('PUT')
            ->add('submit', 'submit', array('label' => 'Inactive'))
            ->getForm()
        ;
    }
    
    public function inactiveSetAction(Request $request){
        $ids = array_keys($request->request->get('rows'));
        if (count($ids) > 0){
            $em = $this->getDoctrine()->getManager();
            $entities = $em->getRepository('ProyBDomainModelBundle:Transaction')->findById($ids);
            foreach ($entities as $entity){
                $this->setInactiveFields($entity);
            }
            $em->flush();
        }
        return $this->redirect($this->generateUrl('transactions'));
    }

    private function createInactiveSetForm(){
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('transaction_inactive_set'))
            ->setMethod('POST')
            ->add('submit', 'submit', array('label' => 'Inactive'))
            ->getForm();
    }
}