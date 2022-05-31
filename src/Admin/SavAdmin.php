<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Article;
use App\Entity\Dealer;
use App\Entity\ProductType;
use App\Entity\Sav;
use App\Entity\Source;
use App\Entity\User;
use App\Enum\CarrierEnum;
use App\Enum\SenderFileEnum;
use App\Form\MyCLabsEnumType;
use App\Handler\RequestForSavHandler;
use App\Library\Autocompleter;
use App\Mailer\Mailer;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\AdminType;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;
use Sonata\Form\Type\CollectionType;
use Sonata\Form\Type\DatePickerType;
use Sonata\Form\Type\DateRangePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

final class SavAdmin extends AbstractAdmin
{
    private $requestForSavHandler;
    private $mailer;

    protected $datagridValues = [

        '_page' => 1,

        '_sort_order' => 'DESC',

        '_sort_by' => 'id',
    ];

    public function getExportFields(): array
    {
        return array_flip([
            'id' => 'ID',
            'createdAtFrench' => 'Date creation',
            'overAtFrench' => 'Date cloture',
            'family' => 'Famille',
            'customerAddressPostCode' => 'Code postal client',
            'customerAddressCity' => 'Ville client',
            'replacementProduct' => 'Produits remplacement',
            'replacementProductName' => 'Designations produits remplacement',
            'serialNumber1' => 'Num serie 1',
            'serialNumber2' => 'Num serie 2',
            'natureSettings' => 'Types de defaut',
            'isReplaced' => 'Remplace',
            'source' => 'Source',
            'dealer' => 'Revendeur',
            'descriptionExport' => 'Commentaire'
        ]);
    }

    public function __construct($code, $class, $baseControllerName, RequestForSavHandler $requestForSavHandler, Mailer $mailer)
    {
        $this->requestForSavHandler = $requestForSavHandler;
        $this->mailer = $mailer;
        parent::__construct($code, $class, $baseControllerName);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id', null, ['label' => 'app.entity.Sav.field.id', 'global_search' => true])
            ->add('createdAt', DateRangeFilter::class, [
                'field_type' => DateRangePickerType::class,
                'label' => 'app.entity.Sav.field.created_at'
            ])
            ->add('source', null, ['label' => 'app.entity.Sav.field.source'])
            ->add('user', null, ['label' => 'app.entity.Sav.field.user'])
            ->add('statusSetting', null, ['label' => 'app.entity.Sav.field.status_setting', 'show_filter' => true])
            ->add('over', null, ['label' => 'app.entity.Sav.field.over'])
            ->add('isNew', null, ['label' => 'app.entity.Sav.field.is_new'])
            ->add('newMessage', null, ['label' => 'app.entity.Sav.field.new_message'])
            ->add('divaltoNumber', null, ['label' => 'app.entity.Sav.field.divalto_number'])
            ->add('family', 'doctrine_orm_callback', [
                'label' => 'app.entity.Sav.field.family',
                'callback' => function ($queryBuilder, $alias, $field, $value) {
                    $queryBuilder->andWhere($alias . '.family = :family')
                        ->setParameter('family', isset($value['value']) && $value['value'] instanceof ProductType ? $value['value']->getCodeLama() : null);
                },
            ], EntityType::class, [
                'class' => ProductType::class,
                'choice_label' => 'codeLama',
                'choice_value' => function (?ProductType $productType) {
                    return $productType ? $productType->getCodeLama() : null;
                },
            ])
            ->add('clientType', null, ['label' => 'app.entity.Sav.field.client_type'])
            ->add('customer.name', null, ['label' => 'app.entity.Sav.field.customer_name'])
            ->add('customer.email', null, ['label' => 'app.entity.Sav.field.customer_email'])
            ->add('dealer', ModelAutocompleteFilter::class, ['label' => 'app.entity.Sav.field.dealer', 'global_search' => true], null, [
                'property' => Autocompleter::dealerAutocomplete(),
            ])
            ->add('newSavs', 'doctrine_orm_callback', array(
                'label' => 'app.entity.Sav.field.new_savs',
                'show_filter' => true,
                'callback' => function ($queryBuilder, $alias, $field, $value) {
                    $queryBuilder->andWhere($alias . '.isNew = :true OR ' . $alias . '.newMessage = :true')
                        ->setParameter('true', true);
                }
            ), ChoiceType::class, array(
                    'choices' => array_flip(array('app.entity.Sav.new'))
                )
            );
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id', null, ['label' => 'app.entity.Sav.field.id'])
            ->add('createdAt', 'date', [
                'label' => 'app.entity.Sav.field.created_at',
                'pattern' => 'dd/MM/Y',
                'local' => 'fr',
                'timezone' => 'Europe/Paris'
            ])
            ->add('source', null, ['label' => 'app.entity.Sav.field.source'])
            ->add('user', null, ['label' => 'app.entity.Sav.field.user'])
            ->add('divaltoNumber', null, ['label' => 'app.entity.Sav.field.divalto_number'])
//            ->add('savArticleString', null, ['label' => 'app.entity.Sav.field.sav_article'])
            ->add('family', null, ['label' => 'app.entity.Sav.field.family'])
            ->add('statusSetting', null, ['label' => 'app.entity.Sav.field.status_setting'])
            ->add('newMessage', null, ['label' => 'app.entity.Sav.field.new_message'])
            ->add('customer.name', 'text', ['label' => 'app.entity.Sav.field.customer_name'])
            ->add('customer.email', 'text', ['label' => 'app.entity.Sav.field.customer_email']);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        /** @var Sav $subject */
        $subject = $this->getSubject();
        $container = $this->getConfigurationPool()->getContainer();

        if (null !== $container && null !== $subject) {
            $security = $container->get('security.token_storage');
            if (null !== $security) {
                $token = $security->getToken();
                if (null !== $token) {
                    /** @var User $user */
                    $user = $token->getUser();
                    if (null !== $user) {
                        $subject->setUser($user);
                    }
                }
            }
        }

        $formMapper
            ->add('createdAt', DatePickerType::class, [
                'label' => 'app.entity.Sav.field.created_at',
                'disabled' => true,
            ])
            ->add('savArticles', CollectionType::class, [
                'by_reference' => false,
                'type_options' => [
                    'delete' => true,
                ]
            ], [
                'edit' => 'inline',
                'inline' => 'standard',
                'sortable' => 'position',
            ])
            ->add('source', ModelAutocompleteType::class, [
                'class' => Source::class,
                'label' => 'app.entity.Sav.field.source',
                'property' => Autocompleter::sourceAutocomplete(),
                'multiple' => false,
                'required' => false,
                'minimum_input_length' => 0
            ])
            ->add('divaltoNumber', null, ['label' => 'app.entity.Sav.field.divalto_number', 'disabled' => true])
            ->add('carrierCode', MyCLabsEnumType::class, [
                'enum' => CarrierEnum::class,
                'label' => 'app.entity.Sav.field.carrier_code',
                'required' => false,
                'preferred_choices' => [CarrierEnum::COLISSIMO],
                'empty_data' => CarrierEnum::COLISSIMO,
                'placeholder' => CarrierEnum::get(CarrierEnum::COLISSIMO)
            ])
//            ->add('clientType', MyCLabsEnumType::class, [
//                'enum' => ClientTypeEnum::class,
//                'label' => 'app.entity.Sav.field.client_type',
//                'required' => false
//            ])
            ->add('statusSetting', ModelType::class, [
                'label' => 'app.entity.Sav.field.status_setting',
                'btn_add' => false,
                'required' => false
            ])
            ->add('customer', AdminType::class, ['label' => 'app.entity.Sav.field.customer'])
            ->add('replacementArticles', ModelAutocompleteType::class, [
                'class' => Article::class,
                'label' => 'app.entity.Sav.field.replacement_article',
                'property' => Autocompleter::articleAutocomplete(),
                'multiple' => true,
                'required' => false,
                'callback' => function (AdminInterface $admin, $property, $value) {
                    $datagrid = $admin->getDatagrid();
                    $value = strtoupper(trim(str_replace(['.', '/', '-', ' '], '', $value)));
                    $queryBuilder = $datagrid->getQuery();
                    $queryBuilder
                        ->where("replace(replace(replace(replace(" . $queryBuilder->getRootAlias() . ".designation, '.', ''), '/', ''), '-', ''), ' ', '') LIKE :term")
                        ->orWhere("replace(replace(replace(replace(" . $queryBuilder->getRootAlias() . ".ean, '.', ''), '/', ''), '-', ''), ' ', '') LIKE :term")
                        ->orWhere("replace(replace(replace(replace(" . $queryBuilder->getRootAlias() . ".reference, '.', ''), '/', ''), '-', ''), ' ', '') LIKE :term")
                        ->setParameter('term', '%' . $value . '%')
                        ->orderBy($queryBuilder->getRootAlias() . '.status', 'DESC');
                },
            ])
            ->add('store', null, ['label' => 'app.entity.Sav.field.store', 'required' => false])
            ->add('family', null, ['label' => 'app.entity.Sav.field.family', 'required' => false])
            ->add('comment', TextareaType::class, ['label' => 'app.entity.Sav.field.comment', 'required' => false])
            ->add('description', null, ['label' => 'app.entity.Sav.field.description', 'required' => false])
            ->add('user', null, ['label' => 'app.entity.Sav.field.user',])
            ->add('dealer', ModelAutocompleteType::class, [
                'class' => Dealer::class,
                'label' => 'app.entity.Sav.field.dealer',
                'property' => Autocompleter::dealerAutocomplete(),
                'multiple' => false,
                'required' => false
            ])
            ->add('messagings', CollectionType::class, [
                'required' => false,
                'by_reference' => false,
                'label' => 'app.entity.Sav.form.message',
                'type_options' => [
                    // Prevents the "Delete" option from being displayed
                    'delete' => false,
                ]
            ], [
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'position',
            ])
            ->add('jiraLink', UrlType::class, ['label' => 'app.entity.Sav.field.jira_link', 'required' => false])
            ->add('customerPrinter', null, ['label' => 'app.entity.Sav.field.customer_printer']);
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
        $collection->add('new_message', 'is-new-message');
        $collection->add('new_sav', 'is-new-sav');
        $collection->add('send_to_divalto', 'send-to-divalto/' . $this->getRouterIdParameter());
        $collection->add('send_mail_commercial', 'send-mail-sav-commercial/' . $this->getRouterIdParameter());
    }

    public function prePersist($object): void
    {
        if (($object instanceof Sav) && $object->getId() === null) {
            $code = $this->requestForSavHandler->generateCode($object);
            $object->setSecretCode($code);
            $this->mailer->sendMailSavNew($object);
        }
    }

    public function preUpdate($object): void
    {
        if ($object instanceof Sav) {
            $this->updateSav($object);
        }
    }

    public function updateSav(Sav $sav): void
    {
        $container = $this->getConfigurationPool()->getContainer();
        if (null === $container) {
            return;
        }
        $em = $container->get('doctrine.orm.entity_manager');
        if (null === $em) {
            return;
        }
        foreach ($sav->getSavArticles() as $savArticle) {
            $article = $savArticle->getArticle();
            if (null !== $article) {
                $family = $article->getProductType();
                if (null !== $family) {
                    $sav->setFamily($family->getCodeLama());
                }
            }
        }
        foreach ($sav->getMessagings() as $message) {
            if (null === $message->getSender()) {
                $message->setSender(SenderFileEnum::LAMA);
            }
            if (null === $message->getMessage()) {
                $sav->removeMessaging($message);
            }

            if (null === $message->getId() && null !== $message->getMessage()) {
                $this->requestForSavHandler->newMessage($sav, $message, false);
            }
        }
    }

    public function getFormTheme()
    {
        return [
            'admin/sav/Form/form_admin_fields.html.twig'
        ];
    }
}
