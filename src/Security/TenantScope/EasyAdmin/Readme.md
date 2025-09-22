# EasyAdmin and tenant scope

We have tested using [EasyAdmin events](https://symfony.com/bundles/EasyAdminBundle/current/events.html)
to set tenants. The problem is that form validation is run prior to any usable events causing validation
to fail and show an validation error to the end user.

The only solution found is to set tenant in `createEntity()` in `App\Controller\Admin\AbstractTenantAwareCrudController`
