export type UserStatus = 'active' | 'inactive';

export type User = {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    status: UserStatus;
    email_verified_at: string | null;
    last_login_at: string | null;
    created_at: string;
    updated_at: string;
    roles?: Role[];
    [key: string]: unknown;
};

export type Role = {
    id: number;
    name: string;
    guard_name: string;
    created_at: string;
    updated_at: string;
    permissions?: Permission[];
};

export type Permission = {
    id: number;
    name: string;
    group: string | null;
    guard_name: string;
    created_at: string;
    updated_at: string;
};

export type Auth = {
    user: User;
};

export type TwoFactorConfigContent = {
    title: string;
    description: string;
    buttonText: string;
};
