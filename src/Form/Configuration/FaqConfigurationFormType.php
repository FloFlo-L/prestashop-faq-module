<?php

declare(strict_types=1);

namespace Module\Faq\Form\Configuration;

use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FaqConfigurationFormType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TranslatableType::class, [
                'label' => $this->trans('Page title', 'Modules.Faq.Admin'),
                'type' => TextType::class,
                'options' => [
                    'attr' => ['maxlength' => 255],
                ],
            ])
            ->add('subtitle', TranslatableType::class, [
                'label' => $this->trans('Page subtitle', 'Modules.Faq.Admin'),
                'type' => TextType::class,
                'options' => [
                    'attr' => ['maxlength' => 255],
                ],
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
