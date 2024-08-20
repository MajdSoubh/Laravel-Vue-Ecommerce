import { createRouter, createWebHistory } from "vue-router";
import store from "../store/index";

/* Admin Auth Routes */
import AdminLogin from "../views/admin/auth/Login.vue";
import AdminSignup from "../views/admin/auth/Signup.vue";
import AdminForgetPassword from "../views/admin/auth/ForgetPassword.vue";
import AdminResetPassword from "../views/admin/auth/ResetPassword.vue";

/* Admin Routes */
import AdminDashboard from "../views/admin/Dashboard.vue";
import AdminProducts from "../views/admin/Products.vue";
import AdminProductForm from "../views/admin/ProductForm.vue";
import AdminUserForm from "../views/admin/UserForm.vue";
import AdminUsers from "../views/admin/Users.vue";
import AdminCategoryForm from "../views/admin/CategoryForm.vue";
import AdminCategories from "../views/admin/Categories.vue";
import AdminReports from "../views/admin/Reports.vue";
import AdminOrders from "../views/admin/Orders.vue";
import AdminGuestLayout from "../layout/admin/GuestLayout.vue";
import AdminDefaultLayout from "../layout/admin/DefaultLayout.vue";

/* User Auth Routes */
import UserLogin from "../views/user/auth/Login.vue";
import UserSignup from "../views/user/auth/Signup.vue";
import UserForgetPassword from "../views/user/auth/ForgetPassword.vue";
import UserResetPassword from "../views/user/auth/ResetPassword.vue";
import UserDefaultLayout from "../layout/user/DefaultLayout.vue";
import Home from "../views/user/Home.vue";
import ProductDetail from "../views/user/ProductDetail.vue";
import Orders from "../views/user/Orders.vue";
import OrderDetail from "../views/user/OrderDetail.vue";
import OrderFulfilment from "../views/user/OrderFulfilment.vue";
import Profile from "../views/user/Profile.vue";

/* Not Found */
import NotFound from "../components/NotFound.vue";

const routes = [
    /* Admin Routes */
    {
        name: "AdminLayout",
        path: "/admin",
        component: AdminDefaultLayout,
        redirect: "/admin/dashboard",
        meta: { requiresAuth: true },
        children: [
            {
                path: "/admin/dashboard",
                name: "admin.dashboard",
                component: AdminDashboard,
            },
            {
                path: "/admin/products",
                name: "admin.product.index",
                component: AdminProducts,
            },
            {
                path: "/admin/orders",
                name: "admin.order.index",
                component: AdminOrders,
            },
            {
                path: "/admin/product/:id?",
                name: "admin.product",
                component: AdminProductForm,
            },
            {
                path: "/admin/users",
                name: "admin.user.index",
                component: AdminUsers,
            },
            {
                path: "/admin/user/:id?",
                name: "admin.user",
                component: AdminUserForm,
            },
            {
                path: "/admin/categories",
                name: "admin.category.index",
                component: AdminCategories,
            },
            {
                path: "/admin/category/:id?",
                name: "admin.category",
                component: AdminCategoryForm,
            },
            {
                path: "/admin/reports",
                name: "admin.reports",
                component: AdminReports,
            },
        ],
    },

    /* Admin Guest Routes */
    {
        name: "AdminGuestLayout",
        path: "/admin",
        component: AdminGuestLayout,
        meta: { requiresGuest: true },
        redirect: "/admin/login",
        children: [
            {
                path: "/admin/login",
                name: "admin.login",
                component: AdminLogin,
            },
            {
                path: "/admin/forget-password",
                name: "admin.forgetPassword",
                component: AdminForgetPassword,
            },
            {
                path: "/admin/Reset-password/:token",
                name: "admin.resetPassword",
                component: AdminResetPassword,
            },
        ],
    },

    /* User Routes */
    {
        name: "DefaultLayout",
        path: "/",
        component: UserDefaultLayout,
        redirect: "/home",
        meta: { requiresAuth: true },
        children: [
            {
                path: "/home",
                name: "home",
                meta: { requiresAuth: false },

                component: Home,
            },

            {
                path: "/product/:slug",
                meta: { requiresAuth: false },
                name: "product",
                component: ProductDetail,
            },
            {
                path: "/orders",
                name: "order.index",
                component: Orders,
            },
            {
                path: "/order/:id",
                name: "order",
                component: OrderDetail,
            },
            {
                path: "/profile",
                name: "profile",
                component: Profile,
            },
        ],
    },

    /* User Guest Routes */
    {
        name: "GuestLayout",
        path: "/",
        component: UserDefaultLayout,
        meta: { requiresGuest: true },
        redirect: "/login",
        children: [
            {
                path: "/login",
                name: "login",
                component: UserLogin,
            },
            {
                path: "/signup",
                name: "signup",
                component: UserSignup,
            },
            {
                path: "/forget-password",
                name: "forgetPassword",
                component: UserForgetPassword,
            },
            {
                path: "/Reset-password/:token",
                name: "resetPassword",
                component: UserResetPassword,
            },
        ],
    },
    {
        path: "/order",
        name: "order.fulfilment",
        component: OrderFulfilment,
    },
    { path: "/:pathMatch(.*)", name: "NotFound", component: NotFound },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// implement is admin
router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth && !store.state.user.token) {
        if (to.name.startsWith("admin")) {
            next({ name: "admin.login" });
        } else {
            next({ name: "login" });
        }
    } else if (to.meta.requiresGuest && store.state.user.token) {
        if (to.name.startsWith("admin") && store.state.user.isAdmin) {
            next({ name: "admin.dashboard" });
        } else {
            next({ name: "home" });
        }
    } else {
        // Procced to the requested location.
        next();
    }
});

export default router;
