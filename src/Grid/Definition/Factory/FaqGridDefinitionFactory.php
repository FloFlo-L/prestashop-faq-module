<?php

declare(strict_types=1);

namespace Module\Faq\Grid\Definition\Factory;

use Module\Faq\Form\ChoiceProvider\FaqCategoryChoiceProvider;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\PositionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FaqGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    public const GRID_ID = 'faq';

    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        private readonly FaqCategoryChoiceProvider $categoryChoiceProvider,
    ) {
        parent::__construct($hookDispatcher);
    }

    protected function getId(): string
    {
        return self::GRID_ID;
    }

    protected function getName(): string
    {
        return $this->trans('FAQ Questions', [], 'Modules.Faq.Admin');
    }

    protected function getFilters(): FilterCollection
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_faq', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => ['placeholder' => $this->trans('Search ID', [], 'Admin.Actions')],
                    ])
                    ->setAssociatedColumn('id_faq')
            )
            ->add(
                (new Filter('question', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => ['placeholder' => $this->trans('Search question', [], 'Admin.Actions')],
                    ])
                    ->setAssociatedColumn('question')
            )
            ->add(
                (new Filter('id_faq_category', ChoiceType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'placeholder' => $this->trans('All categories', [], 'Modules.Faq.Admin'),
                        'choices' => $this->categoryChoiceProvider->getChoices(),
                    ])
                    ->setAssociatedColumn('category_name')
            )
            ->add(
                (new Filter('active', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('active')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => ['filterId' => self::GRID_ID],
                        'redirect_route' => 'faq_question_index',
                    ])
                    ->setAssociatedColumn('actions')
            );
    }

    protected function getBulkActions(): BulkActionCollection
    {
        return (new BulkActionCollection())
            ->add(
                (new SubmitBulkAction('enable_selection'))
                    ->setName($this->trans('Enable selection', [], 'Admin.Actions'))
                    ->setIcon('check_circle')
                    ->setOptions([
                        'submit_route' => 'faq_question_enable_bulk',
                    ])
            )
            ->add(
                (new SubmitBulkAction('disable_selection'))
                    ->setName($this->trans('Disable selection', [], 'Admin.Actions'))
                    ->setIcon('cancel')
                    ->setOptions([
                        'submit_route' => 'faq_question_disable_bulk',
                    ])
            )
            ->add(
                (new SubmitBulkAction('delete_selection'))
                    ->setName($this->trans('Delete selected', [], 'Admin.Actions'))
                    ->setIcon('delete')
                    ->setOptions([
                        'submit_route' => 'faq_question_delete_bulk',
                        'confirm_message' => $this->trans(
                            'Delete selected items?',
                            [],
                            'Admin.Notifications.Warning'
                        ),
                    ])
            );
    }

    protected function getColumns(): ColumnCollection
    {
        return (new ColumnCollection())
            ->add(
                (new BulkActionColumn('bulk'))
                    ->setOptions([
                        'bulk_field' => 'id_faq',
                    ])
            )
            ->add(
                (new DataColumn('id_faq'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions(['field' => 'id_faq'])
            )
            ->add(
                (new DataColumn('category_name'))
                    ->setName($this->trans('Category', [], 'Admin.Global'))
                    ->setOptions(['field' => 'category_name'])
            )
            ->add(
                (new DataColumn('question'))
                    ->setName($this->trans('Question', [], 'Modules.Faq.Admin'))
                    ->setOptions(['field' => 'question'])
            )
            ->add(
                (new PositionColumn('position'))
                    ->setName($this->trans('Position', [], 'Admin.Global'))
                    ->setOptions([
                        'id_field' => 'id_faq',
                        'position_field' => 'position',
                        'update_method' => 'POST',
                        'update_route' => 'faq_question_update_position',
                    ])
            )
            ->add(
                (new ToggleColumn('active'))
                    ->setName($this->trans('Enabled', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'active',
                        'primary_field' => 'id_faq',
                        'route' => 'faq_question_toggle_active',
                        'route_param_name' => 'faqId',
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setOptions([
                        'actions' => (new RowActionCollection())
                            ->add(
                                (new LinkRowAction('edit'))
                                    ->setIcon('edit')
                                    ->setName($this->trans('Edit', [], 'Admin.Actions'))
                                    ->setOptions([
                                        'route' => 'faq_question_edit',
                                        'route_param_name' => 'faqId',
                                        'route_param_field' => 'id_faq',
                                    ])
                            )
                            ->add(
                                (new SubmitRowAction('delete'))
                                    ->setIcon('delete')
                                    ->setName($this->trans('Delete', [], 'Admin.Actions'))
                                    ->setOptions([
                                        'method' => 'POST',
                                        'route' => 'faq_question_delete',
                                        'route_param_name' => 'faqId',
                                        'route_param_field' => 'id_faq',
                                        'confirm_message' => $this->trans(
                                            'Delete selected item?',
                                            [],
                                            'Admin.Notifications.Warning'
                                        ),
                                    ])
                            ),
                    ])
            );
    }
}
