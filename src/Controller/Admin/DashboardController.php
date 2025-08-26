<?php

namespace App\Controller\Admin;

use App\Entity\CommunityEvent\Event;
use App\Entity\CommunityEvent\Source;
use App\Entity\Job;
use App\Entity\News\Entry;
use App\Entity\News\Feed;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

#[AdminDashboard(
    routePath: '/admin',
    routeName: 'admin',
)]
final class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        /** @var AdminUrlGenerator $adminUrlGenerator */
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        return $this->redirect($adminUrlGenerator->setController(JobCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Jobbsy')
            ->generateRelativeUrls();
    }

    public function configureMenuItems(): \Iterator
    {
        yield MenuItem::linkToCrud('Jobs', 'fas fa-code', Job::class);

        yield MenuItem::subMenu('Events & Meetups', 'fas fa-calendar')->setSubItems([
            MenuItem::linkToCrud('Events', 'fas fa-list', Event::class),
            MenuItem::linkToCrud('Sources', 'fas fa-rss', Source::class),
        ]);

        yield MenuItem::subMenu('News', 'fas fa-newspaper')->setSubItems([
            MenuItem::linkToCrud('Entries', 'fas fa-list', Entry::class),
            MenuItem::linkToCrud('Feeds', 'fas fa-rss', Feed::class),
        ]);

        yield MenuItem::section('Channels');
        yield MenuItem::linkToUrl('View website', 'fas fa-external-link', '/');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        $userMenu = parent::configureUserMenu($user);
        $userMenu->setMenuItems([]);

        return $userMenu;
    }
}
