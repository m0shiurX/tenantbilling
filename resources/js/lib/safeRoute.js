export const safeRoute = (path = '#') => {
    if (typeof path !== 'string') {
        return '#';
    }

    return path.startsWith('/') ? path : '#';
};

export const safeCurrentRoute = () => {
    if (typeof window === 'undefined') {
        return '';
    }

    return window.location.pathname || '';
};

export const useCurrentRoute = () => safeCurrentRoute();

export const routeExists = (path = '') =>
    typeof path === 'string' && path.startsWith('/');
