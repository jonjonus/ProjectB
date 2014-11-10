<?php
//echo '<pre>',print_r($arr,1),'</pre>';

namespace ProyB\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use ProyB\DomainModelBundle\Entity\User;
use ProyB\DomainModelBundle\Form\UserType;
use ProyB\SecurityBundle\Form\ChangePasswordType;
use ProyB\SecurityBundle\Form\Model\ChangePassword;

class UserController extends Controller
{
    private function getUrlSuccess(){
        //Si es administrador  
        if (true === $this->get('security.context')->isGranted('ROLE_ADMIN')){
            $url = $this->generateUrl('admin_users');
        }
        else{
           $url = $this->generateUrl('transactions');
        }
        
        return $url;
    }
    
    private function getPathCancel(){
        //Si es administrador  
        if (true === $this->get('security.context')->isGranted('ROLE_ADMIN')){
            return 'admin_users';
        }
        else{
           return 'transactions';
        }
    }
    
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ProyBDomainModelBundle:User')->findAll();

        return $this->render('ProyBWebBundle:User:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    public function createAction(Request $request)
    {
        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('user_show', array('id' => $entity->getId())));
        }

        return $this->render('ProyBWebBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    private function createCreateForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('user_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    public function newAction()
    {
        $entity = new User();
        $form   = $this->createCreateForm($entity);

        return $this->render('ProyBWebBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProyBDomainModelBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProyBWebBundle:User:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function editAction($id = null)
    {
        if (!$id){//es el propio usuario
            $entity = $this->getUser();
            $id = $entity->getId();
        }else{//es ADMIN
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ProyBDomainModelBundle:User')->find($id);
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }
        }
        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);
        return $this->render('ProyBWebBundle:User:edit.html.twig', array('entity'      => $entity,
                                                                        'edit_form'   => $editForm->createView(),
                                                                        'delete_form' => $deleteForm->createView(),
                                                                        'cancel_path'   => $this->getPathCancel(),
                                                                        )
                            );

    }

    private function createEditForm(User $entity)
    {
        //TODO usar UserType o AdminUserType segun corresponda
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('user_update'),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    
    private function createAdminEditForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('user_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    
    public function updateAction(Request $request, $id = null)
    {
        $param = $request->request->get('proyb_domainmodelbundle_user');
        $oldPassword = $param['password'];
        $newPassword = $param['newPassword']['first'];

        if (!$id){
            $id = $this->getUser()->getId();
        }

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ProyBDomainModelBundle:User')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $deleteForm = $this->createDeleteForm($id);

        //encripta la password
        $encoder = $this->get('security.encoder_factory')->getEncoder($entity);
        $encripted = $encoder->encodePassword($newPassword, $entity->getSalt());
        $entity->setPassword($encripted);
        
        //TODO compararlas la passwordOld ingresada con la de la BD
        
        if ($editForm->isValid()) {
            $em->flush();
            return $this->redirect($this->getUrlSuccess());
        }
        
        return $this->render('ProyBWebBundle:User:edit.html.twig', array('entity'       => $entity,
                                                                        'edit_form'     => $editForm->createView(),
                                                                        'delete_form'   => $deleteForm->createView(),
                                                                        'cancel_path'   => $this->getPathCancel(),
                                                                        )
                            );
    }

    
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ProyBDomainModelBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('user'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_user_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    public function changePasswordAction(Request $request)
    {
        $changePasswordModel = new ChangePassword();
        $form = $this->createForm(new ChangePasswordType(), $changePasswordModel);
        $form->add('submit', 'submit', array('label' => 'Update'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ProyBDomainModelBundle:User')->find($this->getUser()->getId());
            
            //encripta la password
            $encoder = $this->get('security.encoder_factory')->getEncoder($entity);
            $encripted = $encoder->encodePassword($changePasswordModel->getNewPassword(), $entity->getSalt());
            $entity->setPassword($encripted);
            
            $em->flush();

          return $this->redirect($this->generateUrl('transactions'));
        }

        return $this->render('ProyBWebBundle:User:changePassword.html.twig', array(
            'change_password_form' => $form->createView(),
        ));      
    }
}