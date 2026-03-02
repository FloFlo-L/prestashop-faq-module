<?php

declare(strict_types=1);

namespace Module\Faq\Form;

use Module\Faq\Form\ChoiceProvider\FaqCategoryChoiceProvider;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class FaqFormType extends TranslatorAwareType
{
    private FaqCategoryChoiceProvider $categoryChoiceProvider;

    public function setCategoryChoiceProvider(FaqCategoryChoiceProvider $categoryChoiceProvider): void
    {
        $this->categoryChoiceProvider = $categoryChoiceProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_faq_category', ChoiceType::class, [
                'label' => $this->trans('Category', 'Admin.Global'),
                'choices' => $this->categoryChoiceProvider->getChoices(),
                'placeholder' => $this->trans('-- Select a category --', 'Modules.Faq.Admin'),
                'constraints' => [new NotBlank()],
                'attr' => [
                    'data-toggle' => 'select2',
                ]
            ])
            ->add('question', TranslatableType::class, [
                'label' => $this->trans('Question', 'Modules.Faq.Admin'),
                'type' => TextType::class,
                'options' => [
                    'constraints' => [new NotBlank()],
                    'attr' => ['maxlength' => 512],
                ],
            ])
            ->add('answer', TranslatableType::class, [
                'label' => $this->trans('Answer', 'Modules.Faq.Admin'),
                'type' => FormattedTextareaType::class,
                'required' => false,
            ])
            ->add('active', SwitchType::class, [
                'label' => $this->trans('Active', 'Admin.Global'),
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'form_theme' => '@PrestaShop/Admin/TwigTemplateForm/prestashop_ui_kit.html.twig',
        ]);
    }
}
