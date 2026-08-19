<!-- component -->
<!--
Change class "fixed" to "sticky" in "navbar" (l. 33) so the navbar doesn't hide any of your page content!
-->
<div>
    <style>
        [x-cloak] { display: none !important; }
        ul.breadcrumb li+li::before {
            content: "";
            padding-left: 8px;
            padding-right: 4px;
            color: inherit;
        }

        ul.breadcrumb li span {
            opacity: 60%;
        }

        #sidebar {
            -webkit-transition: all 300ms cubic-bezier(0, 0.77, 0.58, 1);
            transition: all 300ms cubic-bezier(0, 0.77, 0.58, 1);
        }

        #sidebar.show {
            transform: translateX(0);
        }

        #sidebar ul li a.active {
            background: #1f2937;
            background-color: #1f2937;
        }
    </style>

    <!-- Navbar start -->
    <nav id="navbar" class="fixed top-0 z-40 flex w-full flex-row justify-between bg-gradient-to-r from-gray-400 to-blue-600 px-4 shadow-lg border-b border-gray-300">
        <button id="btnSidebarToggler" type="button" class="py-4 text-2xl text-white hover:text-gray-200">
            <svg id="navClosed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="h-8 w-8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
            <svg id="navOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="hidden h-8 w-8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <a href="<?php echo e(route('dashboard')); ?>" class="py-2 h-1/2">
            <img src="<?php echo e(asset('images/arturo3.png')); ?>" width="150" />
        </a>

        <div class="hidden  md:flex  md:items-center">
            <?php if (isset($component)) { $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown','data' => ['width' => '48']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['width' => '48']); ?>
                 <?php $__env->slot('trigger', null, []); ?> 
                    <!--[if BLOCK]><![endif]--><?php if(Laravel\Jetstream\Jetstream::managesProfilePhotos()): ?>
                        <button
                            class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                            <img class="h-8 w-8 rounded-full object-cover" src="<?php echo e(Auth::user()->profile_photo_url); ?>"
                                alt="<?php echo e(Auth::user()->name); ?>" />
                        </button>
                    <?php else: ?>
                        <span class="inline-flex rounded-md">
                            <button type="button"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white  hover:text-black focus:outline-none transition">
                                <i class="fa-solid fa-user-gear fa-lg"></i>
                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </span>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                 <?php $__env->endSlot(); ?>

                 <?php $__env->slot('content', null, []); ?> 
                    <!-- Account Management -->
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        <?php echo e(__('Administrar Cuenta')); ?>

                    </div>

                    <?php if (isset($component)) { $__componentOriginal68cb1971a2b92c9735f83359058f7108 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal68cb1971a2b92c9735f83359058f7108 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => ''.e(route('profile.show')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('profile.show')).'']); ?>
                        <?php echo e(__('Perfil')); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $attributes = $__attributesOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__attributesOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $component = $__componentOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__componentOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>

                    <!--[if BLOCK]><![endif]--><?php if(Laravel\Jetstream\Jetstream::hasApiFeatures()): ?>
                        <?php if (isset($component)) { $__componentOriginal68cb1971a2b92c9735f83359058f7108 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal68cb1971a2b92c9735f83359058f7108 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => ''.e(route('api-tokens.index')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('api-tokens.index')).'']); ?>
                            <?php echo e(__('API Tokens')); ?>

                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $attributes = $__attributesOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__attributesOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $component = $__componentOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__componentOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <div class="border-t border-gray-100"></div>

                    <!-- Authentication -->
                    <form method="POST" action="<?php echo e(route('logout')); ?>" x-data>
                        <?php echo csrf_field(); ?>

                        <?php if (isset($component)) { $__componentOriginal68cb1971a2b92c9735f83359058f7108 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal68cb1971a2b92c9735f83359058f7108 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => ''.e(route('logout')).'','@click.prevent' => '$root.submit();']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('logout')).'','@click.prevent' => '$root.submit();']); ?>
                            <?php echo e(__('Salir')); ?>

                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $attributes = $__attributesOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__attributesOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $component = $__componentOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__componentOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
                    </form>
                 <?php $__env->endSlot(); ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe)): ?>
<?php $attributes = $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe; ?>
<?php unset($__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldf8083d4a852c446488d8d384bbc7cbe)): ?>
<?php $component = $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe; ?>
<?php unset($__componentOriginaldf8083d4a852c446488d8d384bbc7cbe); ?>
<?php endif; ?>
        </div>

    </nav>
    <!-- Navbar end -->

        <!-- Sidebar start-->
        <div id="containerSidebar" class="z-40">
            <div class="navbar-menu relative z-40">
                <nav id="sidebar" class="fixed left-0 bottom-0 flex w-3/4 -translate-x-full flex-col bg-gradient-to-br from-slate-600 via-blue-700 to-slate-600 pt-2 sm:max-w-xs lg:w-80">
                    <!-- one category / navigation group -->
                    <div class="px-4 overflow-y-auto">
                        <h3 class="mb-2 text-xs font-medium uppercase text-gray-300">
                            Menu principal
                        </h3>
                        <ul class="text-sm font-medium">
                            <li>
                                <a class="flex items-center rounded py-3 pl-3 pr-4  space-x-6 text-gray-50 hover:bg-gray-600"
                                    href="<?php echo e(route('inicio')); ?>">
                                    <i class="fas fa-home -mt-1"></i>
                                    <span class="select-none">Inicio</span>
                                </a>
                            </li>

                            
                            

                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('opciones.caja')): ?>
                                <li class="text-gray-50 py-3 pl-3 pr-4 hover:bg-gray-600 focus:bg-gray-600 rounded"
                                    x-data="{ Open: false }">
                                    <div class="inline-flex  items-center justify-between w-full transition-colors duration-150 text-gray-500  cursor-pointer"
                                        x-on:click="Open = !Open">
                                        <span class="inline-flex items-center space-x-6  text-sm text-white ">
                                            <i class="fa-solid fa-cash-register text-lg"></i>
                                            <span class="select-none">Caja</span>
                                        </span>
                                        <i class="fa-solid fa-caret-down ml-1  text-white w-4 h-4" x-show="!Open"></i>
                                        <i class="fa-solid fa-caret-up ml-1  text-white w-4 h-4" x-show="Open"></i>
                                    </div>
                                    <div x-show.transition="Open" style="display:none;">
                                        <ul x-transition:enter="transition-all ease-in-out duration-300"
                                            x-transition:enter-start="opacity-25 max-h-0"
                                            x-transition:enter-end="opacity-100 max-h-xl"
                                            x-transition:leave="transition-all ease-in-out duration-300"
                                            x-transition:leave-start="opacity-100 max-h-xl"
                                            x-transition:leave-end="opacity-0 max-h-0"
                                            class="mt-2 divide-y-2 divide-gray-600 overflow-hidden text-sm font-medium bg-gray-200 text-white shadow-inner rounded"
                                            aria-label="submenu">

                                            <li class="transition-colors duration-150">
                                                <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('caja.abrir')).'','active' => request()->routeIs('caja.abrir')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('caja.abrir')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('caja.abrir'))]); ?>
                                                    Abrir caja
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                            </li>
                                            <li class="transition-colors duration-150">
                                                <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('caja.cerrar')).'','active' => request()->routeIs('caja.cerrar')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('caja.cerrar')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('caja.cerrar'))]); ?>
                                                    Cerrar caja
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                            </li>
                                            <li class="transition-colors duration-150">
                                                <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('caja.egreso')).'','active' => request()->routeIs('caja.egreso')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('caja.egreso')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('caja.egreso'))]); ?>
                                                    Egreso de caja
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                            </li>
                                            <li class="transition-colors duration-150">
                                                <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('caja.historial')).'','active' => request()->routeIs('caja.historial')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('caja.historial')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('caja.historial'))]); ?>
                                                    Historial de caja
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            <?php endif; ?>

                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('opciones.servicios')): ?>
                                <li class="text-gray-50 py-3 pl-3 pr-4 hover:bg-gray-600 focus:bg-gray-600 rounded"
                                    x-data="{ Open: false }">
                                    <div class="inline-flex  items-center justify-between w-full transition-colors duration-150 text-gray-500  cursor-pointer"
                                        x-on:click="Open = !Open">
                                        <span class="inline-flex items-center space-x-6  text-sm text-white ">
                                            <i class="fa-solid fa-screwdriver-wrench"></i>
                                            <span class="select-none">Servicios</span>
                                        </span>
                                        <i class="fa-solid fa-caret-down ml-1  text-white w-4 h-4" x-show="!Open"></i>
                                        <i class="fa-solid fa-caret-up ml-1  text-white w-4 h-4" x-show="Open"></i>
                                    </div>
                                    <div x-show.transition="Open" style="display:none;">
                                        <ul x-transition:enter="transition-all ease-in-out duration-300"
                                            x-transition:enter-start="opacity-25 max-h-0"
                                            x-transition:enter-end="opacity-100 max-h-xl"
                                            x-transition:leave="transition-all ease-in-out duration-300"
                                            x-transition:leave-start="opacity-100 max-h-xl"
                                            x-transition:leave-end="opacity-0 max-h-0"
                                            class="mt-2 divide-y-2 divide-gray-600 overflow-hidden text-sm font-medium bg-gray-200 text-white shadow-inner rounded"
                                            aria-label="submenu">

                                            <li class="transition-colors duration-150">
                                                <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('ordenes.listado')).'','active' => request()->routeIs('ordenes.listado')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('ordenes.listado')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('ordenes.listado'))]); ?>
                                                    Listado de ordenes                                                
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                            </li>
                                            <li class="transition-colors duration-150">
                                                <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('ordenes.simple.crear')).'','active' => request()->routeIs('ordenes.simple.crear')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('ordenes.simple.crear')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('ordenes.simple.crear'))]); ?>
                                                    Crear orden de servicio
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                            </li>
                                            <li class="transition-colors duration-150">
                                                <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('conversiones.crear')).'','active' => request()->routeIs('conversiones.crear')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('conversiones.crear')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('conversiones.crear'))]); ?>
                                                    Crear orden de conversión
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                            </li>
                                            
                                        </ul>
                                    </div>
                                </li>
                            <?php endif; ?>

                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('opciones.conversiones')): ?>
                                <li class="text-gray-50 py-3 pl-3 pr-4 hover:bg-gray-600 focus:bg-gray-600 rounded"
                                    x-data="{ Open: false }">
                                    <div class="inline-flex  items-center justify-between w-full transition-colors duration-150 text-gray-500  cursor-pointer"
                                        x-on:click="Open = !Open">
                                        <span class="inline-flex items-center space-x-6  text-sm text-white ">
                                            <i class="fa-solid fa-car"></i>
                                            <span class="select-none">Conversiones</span>
                                        </span>
                                        <i class="fa-solid fa-caret-down ml-1  text-white w-4 h-4" x-show="!Open"></i>
                                        <i class="fa-solid fa-caret-up ml-1  text-white w-4 h-4" x-show="Open"></i>
                                    </div>
                                    <div x-show.transition="Open" style="display:none;">
                                        <ul x-transition:enter="transition-all ease-in-out duration-300"
                                            x-transition:enter-start="opacity-25 max-h-0"
                                            x-transition:enter-end="opacity-100 max-h-xl"
                                            x-transition:leave="transition-all ease-in-out duration-300"
                                            x-transition:leave-start="opacity-100 max-h-xl"
                                            x-transition:leave-end="opacity-0 max-h-0"
                                            class="mt-2 divide-y-2 divide-gray-600 overflow-hidden text-sm font-medium bg-gray-200 text-white shadow-inner rounded"
                                            aria-label="submenu">

                                            <!-- Asignar técnicos (Administrador del sistema, Jefe de Taller) -->
                                            
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('conversiones.asignar')): ?>
                                                <li class="transition-colors duration-150">
                                                    <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('conversiones.asignar')).'','active' => request()->routeIs('conversiones.asignar')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('conversiones.asignar')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('conversiones.asignar'))]); ?>
                                                        Asignar técnicos
                                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                                </li>
                                            <?php endif; ?>
                                            <!-- Mis conversiones asignadas (Técnico) -->
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('conversiones.mis-asignadas')): ?>
                                                <li class="transition-colors duration-150">
                                                    <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('conversiones.mis-asignadas')).'','active' => request()->routeIs('conversiones.mis-asignadas')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('conversiones.mis-asignadas')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('conversiones.mis-asignadas'))]); ?>
                                                        Mis conversiones asignadas
                                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                                </li>
                                            <?php endif; ?>                                        
                                            <!-- Entregas pendientes (Administrador del sistema, Jefe de Taller) -->
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('conversiones.entregas-pendientes')): ?>
                                                <li class="transition-colors duration-150">
                                                    <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('conversiones.entregas-pendientes')).'','active' => request()->routeIs('conversiones.entregas-pendientes')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('conversiones.entregas-pendientes')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('conversiones.entregas-pendientes'))]); ?>
                                                        Entregas pendientes
                                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                                </li>
                                            <?php endif; ?>

                                            
                                        </ul>
                                    </div>
                                </li>
                            <?php endif; ?>                            

                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('opciones.almacen')): ?>
                                <li class="text-gray-50 py-3 pl-3 pr-4 hover:bg-gray-600 focus:bg-gray-600 rounded"
                                    x-data="{ Open: false }">
                                    <div class="inline-flex  items-center justify-between w-full transition-colors duration-150 text-gray-500  cursor-pointer"
                                        x-on:click="Open = !Open">
                                        <span class="inline-flex items-center space-x-6  text-sm text-white ">
                                            <i class="fa-solid fa-store"></i>
                                            <span class="select-none">Almacen</span>
                                        </span>
                                        <i class="fa-solid fa-caret-down ml-1  text-white w-4 h-4" x-show="!Open"></i>
                                        <i class="fa-solid fa-caret-up ml-1  text-white w-4 h-4" x-show="Open"></i>
                                    </div>
                                    <div x-show.transition="Open" style="display:none;">
                                        <ul x-transition:enter="transition-all ease-in-out duration-300"
                                            x-transition:enter-start="opacity-25 max-h-0"
                                            x-transition:enter-end="opacity-100 max-h-xl"
                                            x-transition:leave="transition-all ease-in-out duration-300"
                                            x-transition:leave-start="opacity-100 max-h-xl"
                                            x-transition:leave-end="opacity-0 max-h-0"
                                            class="mt-2 divide-y-2 divide-gray-600 overflow-hidden text-sm font-medium bg-gray-200 text-white shadow-inner rounded"
                                            aria-label="submenu">

                                            <li class="transition-colors duration-150">
                                                <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('almacen.categorias.listado')).'','active' => request()->routeIs('almacen.categorias.listado')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('almacen.categorias.listado')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('almacen.categorias.listado'))]); ?>
                                                    Categorías
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                            </li>
                                            <li class="transition-colors duration-150">
                                                <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('almacen.productos.listado')).'','active' => request()->routeIs('almacen.productos.listado')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('almacen.productos.listado')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('almacen.productos.listado'))]); ?>
                                                    Productos
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                            </li>
                                            <li class="transition-colors duration-150">
                                                <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('conversiones.almacen-pendientes')).'','active' => request()->routeIs('conversiones.almacen-pendientes')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('conversiones.almacen-pendientes')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('conversiones.almacen-pendientes'))]); ?>
                                                    Almacen pendientes
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                            </li>
                                            
                                        </ul>
                                    </div>
                                </li>
                            <?php endif; ?>
                            
                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('opciones.rrhh')): ?>
                                <li class="text-gray-50 py-3 pl-3 pr-4 hover:bg-gray-600 focus:bg-gray-600 rounded"
                                    x-data="{ Open: false }">
                                    <div class="inline-flex  items-center justify-between w-full transition-colors duration-150 text-gray-500  cursor-pointer"
                                        x-on:click="Open = !Open">
                                        <span class="inline-flex items-center space-x-6  text-sm text-white ">
                                            <i class="fa-solid fa-flag -mt-1"></i>
                                            <span class="select-none">RRHH</span>
                                        </span>
                                        <i class="fa-solid fa-caret-down ml-1  text-white w-4 h-4" x-show="!Open"></i>
                                        <i class="fa-solid fa-caret-up ml-1  text-white w-4 h-4" x-show="Open"></i>
                                    </div>
                                    <div x-show.transition="Open" style="display:none;">
                                        <ul x-transition:enter="transition-all ease-in-out duration-300"
                                            x-transition:enter-start="opacity-25 max-h-0"
                                            x-transition:enter-end="opacity-100 max-h-xl"
                                            x-transition:leave="transition-all ease-in-out duration-300"
                                            x-transition:leave-start="opacity-100 max-h-xl"
                                            x-transition:leave-end="opacity-0 max-h-0"
                                            class="mt-2 divide-y-2 divide-gray-600 overflow-hidden text-sm font-medium bg-gray-200 text-white shadow-inner rounded"
                                            aria-label="submenu">
                                            
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('rrhh.contratos')): ?>
                                                <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('rrhh.contratos')).'','active' => request()->routeIs('rrhh.contratos')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('rrhh.contratos')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('rrhh.contratos'))]); ?>
                                                    <?php echo e(__('Empleados')); ?>

                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                            <?php endif; ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('rrhh.planillas')): ?>
                                                <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('rrhh.planillas')).'','active' => request()->routeIs('rrhh.planillas')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('rrhh.planillas')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('rrhh.planillas'))]); ?>
                                                    <?php echo e(__('Planillas')); ?>

                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                            <?php endif; ?>
                                            <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('rrhh.mis-planillas')).'','active' => request()->routeIs('rrhh.mis-planillas')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('rrhh.mis-planillas')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('rrhh.mis-planillas'))]); ?>
                                                <?php echo e(__('Boletas')); ?>

                                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>

                                        </ul>

                                    </div>
                                </li>
                            <?php endif; ?>

                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('opciones.reportes')): ?>
                                <li class="text-gray-50 py-3 pl-3 pr-4 hover:bg-gray-600 focus:bg-gray-600 rounded"
                                    x-data="{ Open: false }">
                                    <div class="inline-flex  items-center justify-between w-full transition-colors duration-150 text-gray-500  cursor-pointer"
                                        x-on:click="Open = !Open">
                                        <span class="inline-flex items-center space-x-6  text-sm text-white ">
                                            <i class="fa-solid fa-chart-column"></i>
                                            <span class="select-none">Reportes</span>
                                        </span>
                                        <i class="fa-solid fa-caret-down ml-1  text-white w-4 h-4" x-show="!Open"></i>
                                        <i class="fa-solid fa-caret-up ml-1  text-white w-4 h-4" x-show="Open"></i>
                                    </div>
                                    <div x-show.transition="Open" style="display:none;">
                                        <ul x-transition:enter="transition-all ease-in-out duration-300"
                                            x-transition:enter-start="opacity-25 max-h-0"
                                            x-transition:enter-end="opacity-100 max-h-xl"
                                            x-transition:leave="transition-all ease-in-out duration-300"
                                            x-transition:leave-start="opacity-100 max-h-xl"
                                            x-transition:leave-end="opacity-0 max-h-0"
                                            class="mt-2 divide-y-2 divide-gray-600 overflow-hidden text-sm font-medium bg-gray-200 text-white shadow-inner rounded"
                                            aria-label="submenu">

                                            <li class="transition-colors duration-150">
                                                <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('Rpta.Citas')).'','active' => request()->routeIs('Rpta.Citas')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('Rpta.Citas')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('Rpta.Citas'))]); ?>
                                                    Reporte de Citas
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            <?php endif; ?>

                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('opciones.mantenimientotables')): ?>
                                <li class="text-gray-50 py-3 pl-3 pr-4 hover:bg-gray-600 focus:bg-gray-600 rounded"
                                    x-data="{ Open: false }">
                                    <div class="inline-flex  items-center justify-between w-full transition-colors duration-150 text-gray-500  cursor-pointer"
                                        x-on:click="Open = !Open">
                                        <span class="inline-flex items-center space-x-6  text-sm text-white ">
                                            <i class="fa-solid fa-database"></i>
                                            <span class="select-none">Mantenimiento Tablas</span>
                                        </span>
                                        <i class="fa-solid fa-caret-down ml-1  text-white w-4 h-4" x-show="!Open"></i>
                                        <i class="fa-solid fa-caret-up ml-1  text-white w-4 h-4" x-show="Open"></i>
                                    </div>
                                    <div x-show.transition="Open" style="display:none;">
                                        <ul x-transition:enter="transition-all ease-in-out duration-300"
                                            x-transition:enter-start="opacity-25 max-h-0"
                                            x-transition:enter-end="opacity-100 max-h-xl"
                                            x-transition:leave="transition-all ease-in-out duration-300"
                                            x-transition:leave-start="opacity-100 max-h-xl"
                                            x-transition:leave-end="opacity-0 max-h-0"
                                            class="mt-2 divide-y-2 divide-gray-600 overflow-hidden text-sm font-medium bg-gray-200 text-white shadow-inner rounded"
                                            aria-label="submenu">

                                            <li class="transition-colors duration-150">
                                                <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('ListaVehiculos')).'','active' => request()->routeIs('ListaVehiculos')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('ListaVehiculos')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('ListaVehiculos'))]); ?>
                                                    Lista Vehiculos
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                            </li>
                                            <li class="transition-colors duration-150">
                                                <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('ListaClientes')).'','active' => request()->routeIs('ListaClientes')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('ListaClientes')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('ListaClientes'))]); ?>
                                                    Lista Clientes
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            <?php endif; ?>

                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('opciones.cms')): ?>
                                <li class="text-gray-50 py-3 pl-3 pr-4 hover:bg-gray-600 focus:bg-gray-600 rounded"
                                    x-data="{ Open: false }">
                                    <div class="inline-flex  items-center justify-between w-full transition-colors duration-150 text-gray-500  cursor-pointer"
                                        x-on:click="Open = !Open">
                                        <span class="inline-flex items-center space-x-6  text-sm text-white ">
                                            <i class="fa-solid fa-globe"></i>
                                            <span class="select-none">CMS Landing</span>
                                        </span>
                                        <i class="fa-solid fa-caret-down ml-1  text-white w-4 h-4" x-show="!Open"></i>
                                        <i class="fa-solid fa-caret-up ml-1  text-white w-4 h-4" x-show="Open"></i>
                                    </div>
                                    <div x-show.transition="Open" style="display:none;">
                                        <ul x-transition:enter="transition-all ease-in-out duration-300"
                                            x-transition:enter-start="opacity-25 max-h-0"
                                            x-transition:enter-end="opacity-100 max-h-xl"
                                            x-transition:leave="transition-all ease-in-out duration-300"
                                            x-transition:leave-start="opacity-100 max-h-xl"
                                            x-transition:leave-end="opacity-0 max-h-0"
                                            class="mt-2 divide-y-2 divide-gray-600 overflow-hidden text-sm font-medium bg-gray-200 text-white shadow-inner rounded"
                                            aria-label="submenu">

                                            <li class="transition-colors duration-150">
                                                <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('cms.contenido')).'','active' => request()->routeIs('cms.contenido')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('cms.contenido')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('cms.contenido'))]); ?>
                                                    Contenido Principal
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                            </li>
                                            <li class="transition-colors duration-150">
                                                <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('cms.servicios')).'','active' => request()->routeIs('cms.servicios')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('cms.servicios')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('cms.servicios'))]); ?>
                                                    Servicios
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                            </li>
                                            <li class="transition-colors duration-150">
                                                <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('cms.pasos')).'','active' => request()->routeIs('cms.pasos')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('cms.pasos')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('cms.pasos'))]); ?>
                                                    Proceso / Pasos
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                            </li>
                                            <li class="transition-colors duration-150">
                                                <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('cms.porque')).'','active' => request()->routeIs('cms.porque')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('cms.porque')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('cms.porque'))]); ?>
                                                    Por Qué Elegirnos
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                            </li>
                                            <li class="transition-colors duration-150">
                                                <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('cms.contacto')).'','active' => request()->routeIs('cms.contacto')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('cms.contacto')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('cms.contacto'))]); ?>
                                                    Contacto
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                            </li>
                                            <li class="transition-colors duration-150">
                                                <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('cms.redes')).'','active' => request()->routeIs('cms.redes')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('cms.redes')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('cms.redes'))]); ?>
                                                    Redes Sociales
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                            </li>
                                            <li class="transition-colors duration-150">
                                                <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('cms.apariencia')).'','active' => request()->routeIs('cms.apariencia')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('cms.apariencia')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('cms.apariencia'))]); ?>
                                                    Apariencia
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                            </li>
                                            <li class="transition-colors duration-150">
                                                <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('cms.logo')).'','active' => request()->routeIs('cms.logo')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('cms.logo')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('cms.logo'))]); ?>
                                                    Logo y Favicon
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            <?php endif; ?>

                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('opciones.usuarios')): ?>
                                <li class="text-gray-50 py-3 pl-3 pr-4 hover:bg-gray-600 focus:bg-gray-600 rounded"
                                    x-data="{ Open: false }">
                                    <div class="inline-flex  items-center justify-between w-full  transition-colors duration-150 text-gray-500  cursor-pointer"
                                        x-on:click="Open = !Open">
                                        <span class="inline-flex items-center space-x-6  text-sm  text-white ">
                                            <i class="fa-solid fa-user-shield"></i>
                                            <span class="select-none">Usuarios y roles</span>
                                        </span>
                                        <i class="fa-solid fa-caret-down ml-1  text-white w-4 h-4" x-show="!Open"></i>
                                        <i class="fa-solid fa-caret-up ml-1  text-white w-4 h-4" x-show="Open"></i>
                                    </div>
                                    <div x-show.transition="Open" style="display:none;">
                                        <ul x-transition:enter="transition-all ease-in-out duration-300"
                                            x-transition:enter-start="opacity-25 max-h-0"
                                            x-transition:enter-end="opacity-100 max-h-xl"
                                            x-transition:leave="transition-all ease-in-out duration-300"
                                            x-transition:leave-start="opacity-100 max-h-xl"
                                            x-transition:leave-end="opacity-0 max-h-0"
                                            class="mt-2 divide-y-2 divide-gray-600 overflow-hidden text-sm font-medium bg-gray-200 text-white shadow-inner rounded"
                                            aria-label="submenu">

                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('usuarios')): ?>
                                            <li class="transition-colors duration-150">
                                                <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('usuarios')).'','active' => request()->routeIs('usuarios')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('usuarios')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('usuarios'))]); ?>
                                                    Usuarios
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                            </li>
                                            <?php endif; ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('usuarios.roles')): ?>
                                            <li class="transition-colors duration-150">
                                                <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('usuarios.roles')).'','active' => request()->routeIs('usuarios.roles')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('usuarios.roles')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('usuarios.roles'))]); ?>
                                                    Roles
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                            </li>
                                            <?php endif; ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('usuarios.permisos')): ?>
                                            <li class="transition-colors duration-150">
                                                <?php if (isset($component)) { $__componentOriginald69b52d99510f1e7cd3d80070b28ca18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.responsive-nav-link','data' => ['class' => 'text-sm','href' => ''.e(route('usuarios.permisos')).'','active' => request()->routeIs('usuarios.permisos')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('responsive-nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm','href' => ''.e(route('usuarios.permisos')).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('usuarios.permisos'))]); ?>
                                                    Permisos
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $attributes = $__attributesOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__attributesOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18)): ?>
<?php $component = $__componentOriginald69b52d99510f1e7cd3d80070b28ca18; ?>
<?php unset($__componentOriginald69b52d99510f1e7cd3d80070b28ca18); ?>
<?php endif; ?>
                                            </li>
                                            <?php endif; ?>
                                        </ul>

                                    </div>
                                </li>
                            <?php endif; ?>

                        </ul>
                    </div>



                    <!-- navigation group end-->
                    <!-- opciones de cuenta de usuario -->
                    <div class="md:hidden block bg-gradient-to-br from-gray-400 to-blue-600 bottom-0 left-0 px-4 w-full z-10 mt-2">
                        <h3 class="my-2 text-xs font-medium uppercase text-gray-500">
                            Opciones de la cuenta
                        </h3>
                        <ul class="mb-2 text-sm font-medium ">
                            <li>
                                <a class="flex items-center rounded py-3 pl-3 pr-4  space-x-6 text-gray-50 hover:bg-gray-600 "
                                    href="<?php echo e(route('profile.show')); ?>">
                                    <i class="fa-solid fa-user-gear -mt-1"></i>
                                    <span class="select-none">Configurar Perfil</span>
                                </a>
                            </li>
                            <li>
                                <form method="POST" action="<?php echo e(route('logout')); ?>" x-data>
                                    <?php echo csrf_field(); ?>
                                    <a class="flex items-center rounded py-3 pl-3 pr-4  space-x-6 text-gray-50 hover:bg-gray-600 "
                                        href="<?php echo e(route('logout')); ?>" @click.prevent="$root.submit();">
                                        <i class="fa-solid fa-arrow-right-from-bracket -mt-1"></i>
                                        <span class="select-none">Salir</span>
                                    </a>
                                </form>
                            </li>
                        </ul>
                    </div>
                    <!-- fin -->
                </nav>
            </div>

        </div>
        <!-- Sidebar end -->

    <main>
        <!-- your content goes here -->
    </main>

    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", () => {
            const navbar = document.getElementById("navbar");
            const sidebar = document.getElementById("sidebar");
            const btnSidebarToggler = document.getElementById("btnSidebarToggler");
            const navClosed = document.getElementById("navClosed");
            const navOpen = document.getElementById("navOpen");

            btnSidebarToggler.addEventListener("click", (e) => {
                e.preventDefault();
                sidebar.classList.toggle("show");
                navClosed.classList.toggle("hidden");
                navOpen.classList.toggle("hidden");
            });

            sidebar.style.top = parseInt(navbar.clientHeight) - 1 + "px";
        });
    </script>
</div><?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views/livewire/custom-nav-menu.blade.php ENDPATH**/ ?>