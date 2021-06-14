<?php
declare(strict_types=1);

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SidebarMenu
{

    private FactoryInterface $factory;
    private AuthorizationCheckerInterface $auth;

    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $auth)
    {
        $this->factory = $factory;
        $this->auth = $auth;
    }

    public function build(): ItemInterface
    {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttributes(['class' => 'c-sidebar-nav']);

        $menu->addChild('Dashboard', ['route' => 'home'])
            ->setExtra('icon', 'c-sidebar-nav-icon icon-speedometer')
            ->setAttribute('class', 'c-sidebar-nav-item')
            ->setLinkAttribute('class', 'c-sidebar-nav-link');

        $menu->addChild('Work')->setAttribute('class', 'c-sidebar-nav-title');

        if ($this->auth->isGranted('ROLE_WORK_MANAGE_MEMBERS')) {
            $menu->addChild('Members', ['route' => 'work.members'])
                ->setExtra('routes', [
                    ['route' => 'work.members'],
                    ['pattern' => '/^work.members\..+/']
                ])
                ->setExtra('icon', 'c-sidebar-nav-icon icon-people')
                ->setAttribute('class', 'c-sidebar-nav-item')
                ->setLinkAttribute('class', 'c-sidebar-nav-link');
        }

        if($this->auth->isGranted('ROLE_WORK_MANAGE_PROJECTS')){
            $menu->addChild('Projects', ['route' => 'work.projects'])
                ->setExtra('routes', [
                    ['route' => 'work.projects'],
                    ['pattern' => '/^work.projects\..+/']
                ])
                ->setExtra('icon', 'c-sidebar-nav-icon icon-briefcase')
                ->setAttribute('class', 'c-sidebar-nav-item')
                ->setLinkAttribute('class', 'c-sidebar-nav-link');
        }

        $menu->addChild('Control')->setAttribute('class', 'c-sidebar-nav-title');

        if ($this->auth->isGranted('ROLE_MANAGE_USERS')) {
            $menu->addChild('Users', ['route' => 'users'])
                ->setExtra('icon', 'c-sidebar-nav-icon icon-people')
                ->setExtra('routes', [
                    ['route' => 'users'],
                    ['pattern' => '/^users\..+/']
                ])
                ->setAttribute('class', 'c-sidebar-nav-item')
                ->setLinkAttribute('class', 'c-sidebar-nav-link');
        }

        $menu->addChild('Profile', ['route' => 'profile'])
            ->setExtra('icon', 'c-sidebar-nav-icon icon-user')
            ->setExtra('routes', [
                ['route' => 'profile'],
                ['pattern' => '/^profile\..+/']
            ])
            ->setAttribute('class', 'c-sidebar-nav-item')
            ->setLinkAttribute('class', 'c-sidebar-nav-link');

        return $menu;
    }
}