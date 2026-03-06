<?php

declare(strict_types=1);

namespace Module\Faq\Form;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class FaqCategoryFormType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TranslatableType::class, [
                'label' => $this->trans('Name', 'Admin.Global'),
                'type' => TextType::class,
                'options' => [
                    'constraints' => [new NotBlank()],
                    'attr' => ['maxlength' => 255],
                ],
            ])
            ->add('icon', TextType::class, [
                'label' => $this->trans('Icon', 'Modules.Faq.Admin'),
                'required' => false,
                'attr' => ['maxlength' => 255],
            ])
            ->add('active', SwitchType::class, [
                'label' => $this->trans('Enabled', 'Admin.Global'),
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
