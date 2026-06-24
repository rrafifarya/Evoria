<style>
    .sidebar { 
        background-color: #2d1b4e; 
        min-width: 250px; 
        max-width: 250px;
        min-height: 100vh; 
        color: white; 
        position: sticky;
        top: 0;
        height: 100vh;
        overflow-y: auto;
        flex-shrink: 0;
    }
    
    .sidebar .brand { 
        padding: 18px 20px; 
        border-bottom: 1px solid rgba(255,255,255,0.08); 
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .sidebar .brand img {
        height: 40px;
        width: auto;
        object-fit: contain;
    }
    .sidebar .brand span {
        font-weight: 700;
        font-size: 1.3rem;
        color: white;
        letter-spacing: -0.5px;
        background: linear-gradient(135deg, #a286f4, #6f42c1);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .sidebar .nav-link { 
        color: #d1c1e0; 
        padding: 10px 18px; 
        transition: 0.3s; 
        text-decoration: none; 
        display: block;
        border-radius: 0;
        font-size: 0.9rem;
    }
    .sidebar .nav-link:hover, 
    .sidebar .nav-link.active { 
        background: #3d2a63 !important; 
        color: white !important; 
        border-right: 3px solid #a286f4; 
    }
    .sidebar .menu-label { 
        font-size: 10px; 
        color: #8874a3; 
        text-transform: uppercase; 
        padding: 15px 18px 5px; 
        letter-spacing: 1px; 
        font-weight: 600;
    }
    .sidebar .login-as { 
        margin-top: auto; 
        padding: 15px 18px; 
        background: #251641; 
        border-top: 1px solid rgba(255,255,255,0.05);
    }
    .sidebar .login-as small {
        color: #8874a3 !important;
        font-size: 0.7rem;
    }
    .sidebar .login-as strong {
        color: white;
        font-size: 0.95rem;
    }
    .sidebar .nav-link i {
        width: 18px;
        text-align: center;
        font-size: 0.85rem;
    }
    .sidebar::-webkit-scrollbar {
        width: 4px;
    }
    .sidebar::-webkit-scrollbar-track {
        background: #2d1b4e;
    }
    .sidebar::-webkit-scrollbar-thumb {
        background: #a286f4;
        border-radius: 10px;
    }
    .sidebar::-webkit-scrollbar-thumb:hover {
        background: #8a6fd4;
    }
    .disabled-menu {
        opacity: 0.4;
        cursor: not-allowed;
        pointer-events: none;
    }
    .disabled-menu:hover {
        background: transparent !important;
        border-right: none !important;
    }
    .badge-disabled {
        font-size: 7px;
        padding: 2px 8px;
        background: #dc3545;
        color: white;
        border-radius: 10px;
        margin-left: 5px;
    }
    .menu-label-disabled {
        font-size: 9px;
        color: #dc3545;
        text-transform: uppercase;
        padding: 10px 18px 5px;
        letter-spacing: 0.5px;
        font-weight: 600;
        opacity: 0.6;
    }
</style>