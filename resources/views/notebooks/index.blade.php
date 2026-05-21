<x-app-layout>
    <style>
        .notebooks-page {
            --workspace-primary: #002c76;
            --workspace-primary-deep: #031b4e;
            --workspace-border: rgba(148, 163, 184, 0.2);
            --workspace-shadow: 0 18px 42px rgba(15, 23, 42, 0.06);
        }

        .notebooks-page .container-main {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 0 56px;
        }

        .notebooks-page .section {
            margin-bottom: 56px;
        }

        .notebooks-page .section-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
            margin-bottom: 26px;
        }

        .notebooks-page .section-title-area {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .notebooks-page .section-greeting {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 26px;
            padding: 0.3rem 0.75rem;
            margin-bottom: 8px;
            border-radius: 9999px;
            background: linear-gradient(135deg, rgba(0, 44, 118, 0.12), rgba(91, 124, 250, 0.14));
            color: #1d4ed8;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.78);
        }

        .notebooks-page .section-title {
            margin: 0;
            color: #0f172a;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 22px;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -0.03em;
        }

        .notebooks-page .section-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 14px;
            color: #fff;
            box-shadow: 0 16px 28px rgba(2, 44, 118, 0.18);
        }

        .notebooks-page .section-icon-featured {
            background: linear-gradient(135deg, #1d4ed8 0%, #6d5ef9 100%) !important;
        }

        .notebooks-page .section-icon-recent {
            background: linear-gradient(135deg, #4f46e5 0%, #2563eb 100%) !important;
        }

        .notebooks-page .section-icon-pinned {
            background: linear-gradient(135deg, #0f4ccf 0%, #002c76 100%) !important;
        }

        .notebooks-page .section-icon-shelves {
            background: linear-gradient(135deg, #2563eb 0%, #0f4ccf 100%) !important;
        }

        .notebooks-page .section-icon-shared {
            background: linear-gradient(135deg, #1d4ed8 0%, #4f46e5 100%) !important;
        }

        .notebooks-page .search-wrapper {
            position: relative;
            flex: 1;
            min-width: 240px;
            max-width: 360px;
        }

        .notebooks-page .search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: #64748b;
        }

        .notebooks-page .search-input {
            width: 100%;
            min-height: 52px;
            padding: 0.8rem 1rem 0.8rem 2.9rem;
            border: 1px solid rgba(191, 219, 254, 0.95);
            border-radius: 18px;
            background: rgba(243, 247, 253, 0.98);
            color: #0f172a;
            font-family: 'Manrope', sans-serif;
            font-size: 14px;
            outline: none;
            transition: all 0.2s ease;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.82);
        }

        .notebooks-page .search-input::placeholder {
            color: #94a3b8;
        }

        .notebooks-page .search-input:focus {
            border-color: rgba(29, 78, 216, 0.65);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(0, 44, 118, 0.08), 0 12px 24px rgba(37, 99, 235, 0.06);
        }

        .notebooks-page .notebooks-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
        }

        .notebooks-page .notebooks-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .notebooks-page .list-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 18px 20px;
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
            transition: transform 0.24s ease, box-shadow 0.24s ease, border-color 0.24s ease;
        }

        .notebooks-page .list-item:hover {
            transform: translateY(-1px);
            border-color: rgba(191, 219, 254, 1);
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        }

        .notebooks-page .list-cover,
        .notebooks-page .notebook-cover,
        .notebooks-page .shelf-notebook-cover {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #fff !important;
            background: linear-gradient(135deg, #1d4ed8 0%, #6d5ef9 54%, #4f46e5 100%) !important;
            box-shadow: 0 16px 30px rgba(37, 99, 235, 0.2);
        }

        .notebooks-page .list-cover {
            width: 40px;
            height: 40px;
            border-radius: 12px;
        }

        .notebooks-page .notebook-cover {
            width: 48px;
            height: 48px;
            border-radius: 16px;
        }

        .notebooks-page .shelf-notebook-cover {
            width: 34px;
            height: 34px;
            border-radius: 10px;
        }

        .notebooks-page .list-content {
            min-width: 0;
            flex: 1;
        }

        .notebooks-page .list-title {
            margin-bottom: 4px;
            color: #0f172a;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .notebooks-page .list-meta {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 12px;
            color: #64748b;
            flex-wrap: wrap;
        }

        .notebooks-page .notebook-card {
            position: relative;
            overflow: hidden;
            padding: 22px;
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 28px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(250, 252, 255, 0.96) 100%);
            box-shadow: var(--workspace-shadow);
            transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
        }

        .notebooks-page .notebook-card:hover {
            transform: translateY(-2px);
            border-color: rgba(191, 219, 254, 1);
            box-shadow: 0 20px 46px rgba(15, 23, 42, 0.08);
        }

        .notebooks-page .notebook-card::after {
            content: '';
            position: absolute;
            inset: auto 0 0 0;
            height: 36px;
            background: linear-gradient(180deg, transparent 0%, rgba(91, 124, 250, 0.08) 100%);
            pointer-events: none;
        }

        .notebooks-page .notebook-card.create {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            min-height: 240px;
            border: 1.5px dashed rgba(191, 219, 254, 0.95);
            background: linear-gradient(180deg, rgba(247, 249, 253, 0.92) 0%, rgba(255, 255, 255, 0.96) 100%);
        }

        .notebooks-page .create-icon-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            border-radius: 18px;
            background: linear-gradient(135deg, #1d4ed8 0%, #6d5ef9 100%) !important;
            color: #fff;
            box-shadow: 0 18px 32px rgba(37, 99, 235, 0.18);
        }

        .notebooks-page .create-icon {
            width: 24px;
            height: 24px;
            color: currentColor;
        }

        .notebooks-page .create-text {
            color: #0f172a;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .notebooks-page .create-subtext {
            font-size: 13px;
            color: #64748b;
        }

        .notebooks-page .notebook-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
        }

        .notebooks-page .notebook-title {
            margin-bottom: 12px;
            color: #0f172a;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 16px;
            font-weight: 700;
            line-height: 1.45;
            letter-spacing: -0.03em;
        }

        .notebooks-page .notebook-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding-top: 14px;
            border-top: 1px solid rgba(226, 232, 240, 0.8);
        }

        .notebooks-page .notebook-category,
        .notebooks-page .notebook-owner {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
        }

        .notebooks-page .owner-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border-radius: 9999px;
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%) !important;
            color: #fff;
            font-size: 11px;
            font-weight: 800;
            box-shadow: 0 12px 22px rgba(37, 99, 235, 0.18);
        }

        .notebooks-page .shelf-card {
            overflow: hidden;
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.94);
            box-shadow: var(--workspace-shadow);
        }

        .notebooks-page .shelf-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 20px;
            border-bottom: 1px solid rgba(241, 245, 249, 0.92);
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .notebooks-page .shelf-header:hover {
            background: rgba(248, 250, 252, 0.9);
        }

        .notebooks-page .shelf-header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .notebooks-page .shelf-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 12px;
            font-size: 18px;
        }

        .notebooks-page .shelf-title {
            color: #0f172a;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .notebooks-page .shelf-count {
            font-size: 12px;
            color: #64748b;
        }

        .notebooks-page .shelf-content {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
            padding: 16px 20px 20px;
        }

        .notebooks-page .shelf-notebook {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 14px;
            background: rgba(248, 250, 252, 0.95);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .notebooks-page .shelf-notebook:hover {
            background: #f1f5f9;
        }

        .notebooks-page .shelf-notebook-title {
            color: #0f172a;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.3;
        }

        .notebooks-page .btn-add-shelf {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 22px;
            border: 1.5px dashed rgba(191, 219, 254, 0.95);
            border-radius: 20px;
            background: rgba(248, 250, 252, 0.94);
            color: #0f172a;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 15px;
            font-weight: 700;
            transition: all 0.2s ease;
        }

        .notebooks-page .btn-add-shelf:hover {
            border-color: rgba(147, 197, 253, 1);
            background: rgba(239, 246, 255, 0.9);
        }

        .notebooks-page .pin-badge,
        .notebooks-page .featured-badge {
            position: absolute;
            top: 14px;
            right: 14px;
            padding: 6px 10px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #fff;
            box-shadow: 0 14px 24px rgba(15, 23, 42, 0.12);
        }

        .notebooks-page .pin-badge {
            background: linear-gradient(135deg, #0f4ccf 0%, #002c76 100%);
        }

        .notebooks-page .featured-badge {
            background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
        }

        .notebooks-page .shared-role {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 10px;
            border-radius: 9999px;
            background: rgba(29, 78, 216, 0.08) !important;
            color: #1d4ed8 !important;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .notebooks-page .empty-state {
            padding: 40px 20px;
            border: 1px dashed rgba(191, 219, 254, 0.95);
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.92);
            text-align: center;
        }

        .notebooks-page .empty-state-title {
            margin-bottom: 6px;
            color: #0f172a;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 16px;
            font-weight: 700;
        }

        .notebooks-page .empty-state-text {
            font-size: 13px;
            color: #64748b;
        }

        .notebooks-page .status-message {
            animation: fadeOut 4s ease-in-out forwards;
            animation-delay: 2s;
        }

        @keyframes fadeOut {
            0% { opacity: 1; }
            70% { opacity: 1; }
            100% { opacity: 0; display: none; }
        }

        .notebooks-page .filter-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            padding: 0.8rem 1.25rem;
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.7);
            color: #475569;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: -0.01em;
            cursor: pointer;
            transition: all 0.22s ease;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
        }

        .notebooks-page .filter-btn:hover {
            border-color: rgba(191, 219, 254, 1);
            color: #0f172a;
            transform: translateY(-1px);
        }

        .notebooks-page .filter-btn.active {
            border-color: transparent;
            background: linear-gradient(135deg, #0f4ccf 0%, #002c76 100%);
            color: #fff;
            box-shadow: 0 16px 30px rgba(2, 44, 118, 0.24);
        }

        .notebooks-page .view-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            min-width: 48px;
            min-height: 48px;
            padding: 0.7rem 0.85rem;
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.82);
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .notebooks-page .view-btn:hover {
            border-color: rgba(191, 219, 254, 1);
            color: #0f172a;
        }

        .notebooks-page .view-btn.active {
            border-color: transparent;
            background: linear-gradient(135deg, #0f4ccf 0%, #002c76 100%);
            color: #fff;
            box-shadow: 0 14px 24px rgba(2, 44, 118, 0.2);
        }

        .notebooks-page .workspace-toolbar-stack,
        .notebooks-page .workspace-controls-row,
        .notebooks-page .workspace-filter-row,
        .notebooks-page .workspace-controls-left {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
        }

        .notebooks-page .workspace-toolbar-stack {
            flex-direction: column;
            gap: 24px;
        }

        .notebooks-page .workspace-filter-row {
            align-items: center;
            gap: 18px;
        }

        .notebooks-page .workspace-controls-row {
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }

        .notebooks-page .workspace-controls-left {
            align-items: center;
            gap: 16px;
        }

        .notebooks-page .workspace-view-toggle,
        .notebooks-page .workspace-sort-control {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px;
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.9) !important;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
        }

        .notebooks-page .workspace-sort-control {
            padding: 0.75rem 1rem;
            min-height: 52px;
        }

        .notebooks-page .workspace-sort-control select {
            border: 0;
            background: transparent;
            color: #334155;
            font-family: 'Manrope', sans-serif;
            font-size: 14px;
            font-weight: 700;
            outline: none;
            cursor: pointer;
        }

        .notebooks-page .btn-notebook-create {
            min-height: 52px;
            padding: 0.95rem 1.25rem;
            border-radius: 18px;
            box-shadow: 0 18px 36px rgba(2, 44, 118, 0.22);
        }

        .notebooks-page .btn-notebook-create:hover {
            transform: translateY(-1px);
        }

        .notebooks-page .bulk-actions-bar {
            border: 1px solid rgba(226, 232, 240, 0.95);
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 24px 52px rgba(15, 23, 42, 0.12);
        }

        .notebooks-page .modal-overlay {
            z-index: 200;
            background: rgba(15, 23, 42, 0.52) !important;
            backdrop-filter: blur(12px);
        }

        .notebooks-page .modal-shell {
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 24px;
            padding: 28px;
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 24px 52px rgba(15, 23, 42, 0.12);
        }

        .notebooks-page .modal-shell h3 {
            margin: 0 0 20px;
            color: #0f172a;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.03em;
        }

        .notebooks-page .modal-shell label {
            display: block;
            margin-bottom: 8px;
            color: #334155;
            font-family: 'Manrope', sans-serif;
            font-size: 13px;
            font-weight: 700;
        }

        .notebooks-page .modal-shell input[type="text"] {
            width: 100%;
            padding: 0.85rem 1rem;
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 14px;
            background: #fff;
            color: #0f172a;
            font-family: 'Manrope', sans-serif;
            font-size: 14px;
            outline: none;
            transition: all 0.2s ease;
        }

        .notebooks-page .modal-shell input[type="text"]:focus {
            border-color: rgba(29, 78, 216, 0.6);
            box-shadow: 0 0 0 4px rgba(0, 44, 118, 0.08);
        }

        .notebooks-page .modal-action-row {
            display: flex;
            gap: 10px;
        }

        .notebooks-page .modal-button {
            flex: 1;
            min-height: 48px;
            padding: 0.85rem 1.25rem;
            border-radius: 14px;
            font-family: 'Manrope', sans-serif;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .notebooks-page .modal-button-primary {
            border: none;
            background: linear-gradient(135deg, #0f4ccf 0%, #002c76 100%);
            color: #fff;
            box-shadow: 0 16px 30px rgba(2, 44, 118, 0.2);
        }

        .notebooks-page .modal-button-primary:hover {
            transform: translateY(-1px);
        }

        .notebooks-page .modal-button-secondary {
            border: 1px solid rgba(226, 232, 240, 0.95);
            background: #fff;
            color: #0f172a;
        }

        .notebooks-page .modal-button-secondary:hover {
            background: #f8fafc;
        }
    </style>

    <div class="notebooks-page container-main" x-data="notebookIndex()">
        <script>
            function notebookIndex() {
                return {
                    userMenuOpen: false, 
                    showRenameModal: false,
                    renameNotebookId: null,
                    renameTitle: '',
                    searchQuery: '',
                    viewMode: 'grid',
                    sortBy: 'recent',
                    currentFilter: 'all',
                    allNotebooks: @json($userNotebooks),
                    featuredNotebooksData: @json($featuredNotebooks),
                    pinnedNotebooksData: @json($pinnedNotebooks),
                    shelvesData: @json($shelves),
                    sharedNotebooksData: @json($sharedNotebooks),
                    selectedNotebooks: [],
                    bulkAction: '',
                    showBulkActions: false,
                    showCreateShelfModal: false,
                    newShelfName: '',
                    newShelfIcon: '📚',
                    expandedShelves: {},

                    toggleNotebook(id) {
                        const index = this.selectedNotebooks.indexOf(id);
                        if (index > -1) {
                            this.selectedNotebooks.splice(index, 1);
                        } else {
                            this.selectedNotebooks.push(id);
                        }
                        this.showBulkActions = this.selectedNotebooks.length > 0;
                    },
                    
                    toggleSelectAll() {
                        if (this.selectedNotebooks.length === this.displayNotebooks.length) {
                            this.selectedNotebooks = [];
                        } else {
                            this.selectedNotebooks = this.displayNotebooks.map(n => n.id);
                        }
                        this.showBulkActions = this.selectedNotebooks.length > 0;
                    },
                    
                    get allSelected() {
                        return this.displayNotebooks.length > 0 && 
                            this.selectedNotebooks.length === this.displayNotebooks.length;
                    },

                    get displayNotebooks() {
                        let notebooks = [];

                        if (this.currentFilter === 'all') {
                            notebooks = [...this.allNotebooks];
                        } else if (this.currentFilter === 'my') {
                            notebooks = [...this.allNotebooks];
                        } else if (this.currentFilter === 'featured') {
                            notebooks = [...this.featuredNotebooksData];
                        }

                        if (this.searchQuery.trim() !== '') {
                            const q = this.searchQuery.toLowerCase();
                            notebooks = notebooks.filter(n => 
                                n.title.toLowerCase().includes(q)
                            );
                        }

                        if (this.sortBy === 'title') {
                            notebooks.sort((a, b) => a.title.localeCompare(b.title));
                        } else if (this.sortBy === 'oldest') {
                            notebooks.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                        } else {
                            notebooks.sort((a, b) => new Date(b.last_activity_at) - new Date(a.last_activity_at));
                        }

                        return notebooks;
                    },

                    get showRecent() {
                        return this.currentFilter === 'all' || this.currentFilter === 'my';
                    },

                    get showPinned() {
                        return (this.currentFilter === 'all' || this.currentFilter === 'my') && this.pinnedNotebooksData.length > 0;
                    },

                    get showShelves() {
                        return this.currentFilter === 'all' || this.currentFilter === 'my';
                    },

                    get showFeatured() {
                        return this.currentFilter === 'all' || this.currentFilter === 'featured';
                    },

                    toggleShelf(shelfId) {
                        this.expandedShelves[shelfId] = !this.expandedShelves[shelfId];
                    }
                }
            }
        </script>

        @if (session('status'))
            <div class="mb-6 rounded-xl border-emerald-200 bg-emerald-50 px-5 py-3 text-sm text-emerald-800 font-medium status-message">
                {{ session('status') }}
            </div>
        @endif

        <!-- Top Bar with Filters & Views -->
        <div class="mb-8">
            <div class="workspace-toolbar-stack">
                <!-- Tabs: All / My notebooks / Featured notebooks -->
                <div class="workspace-filter-row">
                    <button type="button" :class="currentFilter === 'all' ? 'filter-btn active' : 'filter-btn'" @click="currentFilter = 'all'">
                        All
                    </button>
                    <button type="button" :class="currentFilter === 'my' ? 'filter-btn active' : 'filter-btn'" @click="currentFilter = 'my'">
                        My notebooks
                    </button>
                    @if ($featuredNotebooks->isNotEmpty())
                        <button type="button" :class="currentFilter === 'featured' ? 'filter-btn active' : 'filter-btn'" @click="currentFilter = 'featured'">
                            Featured notebooks
                        </button>
                    @endif
                </div>

                <!-- View Controls & Actions -->
                <div class="workspace-controls-row">
                    <!-- Left: Search + View + Sort -->
                    <div class="workspace-controls-left">
                        <!-- Search -->
                        <div class="search-wrapper" style="max-width: 400px;">
                            <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" class="search-input" placeholder="Search notebooks..." x-model="searchQuery">
                        </div>

                        <!-- View Toggle -->
                        <div class="workspace-view-toggle">
                            <button type="button" :class="viewMode === 'grid' ? 'view-btn active' : 'view-btn'" @click="viewMode = 'grid'">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px; height:18px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                </svg>
                            </button>
                            <button type="button" :class="viewMode === 'list' ? 'view-btn active' : 'view-btn'" @click="viewMode = 'list'">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px; height:18px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Sort Dropdown -->
                        <div class="workspace-sort-control">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px; height:18px; color:#64748b;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <select x-model="sortBy">
                                <option value="recent">Most recent</option>
                                <option value="title">Name A-Z</option>
                                <option value="oldest">Oldest</option>
                            </select>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px; height:16px; color:#64748b;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Right: Create New Button -->
                    <form method="POST" action="{{ route('notebooks.create.quick') }}">
                        @csrf
                        <button type="submit" class="btn-premium btn-notebook-create">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px; height:18px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Create new
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Featured Notebooks (shown when filter is 'all' or 'featured') -->
        <div x-show="showFeatured" style="display: none;" x-transition>
            @if ($featuredNotebooks->isNotEmpty())
                <div class="section">
                    <div class="section-header">
                        <div class="section-title-area">
                            <div class="section-icon section-icon-featured" style="background: linear-gradient(135deg, #ec4899, #db2777);">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.95 1.71l-1.52 4.674c-.3.921-1.604.921-1.902 0l-5.449-1.675a1 1 0 00-.95.69h-4.915c-.969 0-1.371-1.24-.95-1.71l1.52-4.674a1 1 0 00-.95-.69H5.183c-.969 0-1.371 1.24-.95 1.71l1.519 4.674c.3.921 1.603.921 1.902 0l5.45 1.675c.3.921 1.604-.921 1.902 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="section-greeting">Featured</div>
                                <h2 class="section-title">Featured Notebooks</h2>
                            </div>
                        </div>
                    </div>

                    <div x-show="viewMode === 'grid'" class="notebooks-grid">
                        @foreach ($featuredNotebooks as $notebook)
                            <div class="notebook-card" style="cursor: default;">
                                <div class="featured-badge">Featured</div>
                                <div class="notebook-header" style="gap: 8px;">
                                    <a href="{{ route('notebooks.show', $notebook) }}" class="notebook-cover" style="background: {{ $notebook->cover_color ?? '#1d4ed8' }}; text-decoration: none; color: inherit;">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </a>
                                </div>
                                <a href="{{ route('notebooks.show', $notebook) }}" style="text-decoration: none; color: inherit;">
                                    <div class="notebook-title">{{ $notebook->title }}</div>
                                    <div class="notebook-meta">
                                        <div class="notebook-category">
                                            <svg fill="currentColor" viewBox="0 0 24 24" style="width:13px; height:13px;">
                                                <path d="M3 7V5c0-1.1.9-2 2-2h4l2 2h8c1.1 0 2 .9 2 2v2H3zm0 12h18V9H3v10z"/>
                                            </svg>
                                            <span>{{ $notebook->category->name ?? 'Projects' }}</span>
                                        </div>
                                        <div class="notebook-owner">
                                            <div class="owner-avatar">
                                                {{ strtoupper(substr($notebook->owner->name ?? 'N', 0, 1)) }}
                                            </div>
                                            <span>{{ $notebook->owner->name ?? 'User' }}</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <div x-show="viewMode === 'list'" class="notebooks-list" style="display: none;">
                        @foreach ($featuredNotebooks as $notebook)
                            <div class="list-item">
                                <a href="{{ route('notebooks.show', $notebook) }}" class="list-cover" style="background: {{ $notebook->cover_color ?? '#1d4ed8' }}; text-decoration: none; color: inherit;">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </a>
                                <div class="list-content">
                                    <a href="{{ route('notebooks.show', $notebook) }}" style="text-decoration: none; color: inherit;">
                                        <div class="list-title">{{ $notebook->title }}</div>
                                    </a>
                                    <div class="list-meta">
                                        <span>{{ $notebook->category->name ?? 'Projects' }}</span>
                                        <span>Created {{ \Carbon\Carbon::parse($notebook->created_at)->format('j M Y') }}</span>
                                        <span>Owner: {{ $notebook->owner->name ?? 'User' }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Recent Notebooks (shown when filter is 'all' or 'my') -->
        <div x-show="showRecent" style="display: none;" x-transition>
            <div class="section">
                <template x-if="showBulkActions">
                    <form method="POST" action="{{ route('notebooks.bulk') }}" class="flex items-center gap-3 mb-6 p-4 bg-slate-50 border border-slate-200 rounded-xl w-full">
                        @csrf
                        <template x-for="id in selectedNotebooks" :key="id">
                            <input type="hidden" name="notebook_ids[]" :value="id">
                        </template>
                        <span class="text-slate-700 font-medium text-sm" x-text="selectedNotebooks.length + ' selected'"></span>
                        <select name="action" x-model="bulkAction" required class="bg-white border border-slate-200 text-slate-900 rounded-lg px-3 py-2 text-sm">
                            <option value="">Select action</option>
                            <option value="delete">Delete</option>
                            <option value="pin">Pin</option>
                            <option value="unpin">Unpin</option>
                            <option value="share">Share</option>
                        </select>
                        <button type="submit" :disabled="!bulkAction" class="btn-premium text-sm" onclick="return confirm('Are you sure you want to perform this action?')">Apply</button>
                        <button type="button" @click="selectedNotebooks = []; showBulkActions = false;" class="btn-premium-outline text-sm">Clear</button>
                    </form>
                </template>

                <div class="section-header">
                    <div class="section-title-area">
                        <div class="section-icon section-icon-recent" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="section-greeting">Recent</div>
                            <h2 class="section-title">Recent Notebooks</h2>
                        </div>
                    </div>
                </div>

                <!-- Grid View -->
                <div x-show="viewMode === 'grid'" class="notebooks-grid">
                    <form method="POST" action="{{ route('notebooks.create.quick') }}" class="notebook-card create">
                        @csrf
                        <button
                            type="submit"
                            style="all: unset; width: 100%; height: 100%; cursor: pointer; display: flex; flex-direction: column; align-items: center;"
                            aria-label="Create new notebook"
                        >
                            <div class="create-icon-wrapper">
                                <svg class="create-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                            <div class="create-text">Create notebook</div>
                            <div class="create-subtext">Start from scratch</div>
                        </button>
                    </form>

                    <template x-for="notebook in displayNotebooks" :key="notebook.id">
                        <div class="notebook-card" style="cursor: default;">
                            <div class="notebook-header" style="gap: 8px;">
                                <input type="checkbox" :checked="selectedNotebooks.includes(notebook.id)" @click.stop="toggleNotebook(notebook.id)" class="w-4 h-4 rounded" style="accent-color: #1d4ed8;">
                                <a :href="`/notebooks/${notebook.id}`" class="notebook-cover" :style="{ background: notebook.cover_color || '#1d4ed8' }" style="text-decoration: none; color: inherit;">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </a>
                            </div>
                            <a :href="`/notebooks/${notebook.id}`" style="text-decoration: none; color: inherit;">
                                <div class="notebook-title" x-text="notebook.title"></div>
                                <div class="notebook-meta">
                                    <div class="notebook-category">
                                        <svg fill="currentColor" viewBox="0 0 24 24" style="width:13px; height:13px;">
                                            <path d="M3 7V5c0-1.1.9-2 2-2h4l2 2h8c1.1 0 2 .9 2 2v2H3zm0 12h18V9H3v10z"/>
                                        </svg>
                                        <span x-text="notebook.category?.name || 'Projects'"></span>
                                    </div>
                                    <div class="notebook-owner">
                                        <div class="owner-avatar">
                                            <span x-text="(notebook.owner?.name || 'U').charAt(0).toUpperCase()"></span>
                                        </div>
                                        <span>You</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </template>
                </div>

                <!-- List View -->
                <div x-show="viewMode === 'list'" class="notebooks-list" style="display: none;">
                    <form method="POST" action="{{ route('notebooks.create.quick') }}" class="list-item" style="border: 1.5px dashed rgba(191, 219, 254, 0.95); background: rgba(248, 250, 252, 0.95); cursor: pointer;">
                        @csrf
                        <button type="submit" style="all: unset; width: 100%; display: flex; align-items: center; gap: 16px;">
                            <div class="create-icon-wrapper" style="width: 40px; height: 40px;">
                                <svg class="create-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                            <div style="text-align: left;">
                                <div class="create-text">Create notebook</div>
                                <div class="create-subtext">Start from scratch</div>
                            </div>
                        </button>
                    </form>

                    <template x-for="notebook in displayNotebooks" :key="notebook.id">
                        <div class="list-item">
                            <input type="checkbox" :checked="selectedNotebooks.includes(notebook.id)" @click.stop="toggleNotebook(notebook.id)" class="w-4 h-4 rounded" style="accent-color: #1d4ed8;">
                            <a :href="`/notebooks/${notebook.id}`" class="list-cover" :style="{ background: notebook.cover_color || '#1d4ed8' }" style="text-decoration: none; color: inherit;">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </a>
                            <div class="list-content">
                                <a :href="`/notebooks/${notebook.id}`" style="text-decoration: none; color: inherit;">
                                    <div class="list-title" x-text="notebook.title"></div>
                                </a>
                                <div class="list-meta">
                                    <span x-text="notebook.category?.name || 'Projects'"></span>
                                    <span x-text="'Created ' + new Date(notebook.created_at).toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' })"></span>
                                    <span>Owner: You</span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Pinned Notebooks (shown when filter is 'all' or 'my') -->
        <div x-show="showPinned" style="display: none;" x-transition>
            @if ($pinnedNotebooks->isNotEmpty())
                <div class="section">
                <div class="section-header">
                    <div class="section-title-area">
                        <div class="section-icon section-icon-pinned" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                            </svg>
                            </div>
                            <div>
                                <div class="section-greeting">Pinned</div>
                                <h2 class="section-title">Pinned Notebooks</h2>
                            </div>
                        </div>
                    </div>

                    <div x-show="viewMode === 'grid'" class="notebooks-grid">
                        @foreach ($pinnedNotebooks as $notebook)
                            <div class="notebook-card" style="cursor: default;">
                                <div class="pin-badge">Pinned</div>
                                <div class="notebook-header" style="gap: 8px;">
                                    <a href="{{ route('notebooks.show', $notebook) }}" class="notebook-cover" style="background: {{ $notebook->cover_color ?? '#1d4ed8' }}; text-decoration: none; color: inherit;">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </a>
                                    <div x-data="{ notebookMenuOpen: false }" class="relative">
                                        <button @click="notebookMenuOpen = !notebookMenuOpen" class="notebook-menu-btn">
                                            <svg fill="currentColor" viewBox="0 0 24 24" style="width:18px; height:18px;">
                                                <circle cx="12" cy="6" r="2"/>
                                                <circle cx="12" cy="12" r="2"/>
                                                <circle cx="12" cy="18" r="2"/>
                                            </svg>
                                        </button>
                                        <div x-show="notebookMenuOpen" @click.outside="notebookMenuOpen = false" style="position:absolute; top:28px; right:0; background:white; border:1px solid #e2e8f0; border-radius:10px; box-shadow:0 4px 12px rgba(15,23,42,0.1); min-width:150px; z-index:100;">
                                            <form method="POST" action="{{ route('notebooks.unpin', $notebook) }}">
                                                @csrf
                                                <button type="submit" style="width:100%; padding:10px 14px; text-align:left; background:none; border:none; cursor:pointer; font-size:13px; font-weight:500; color:#0f172a;">
                                                    Unpin
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('notebooks.show', $notebook) }}" style="text-decoration: none; color: inherit;">
                                    <div class="notebook-title">{{ $notebook->title }}</div>
                                    <div class="notebook-meta">
                                        <div class="notebook-category">
                                            <svg fill="currentColor" viewBox="0 0 24 24" style="width:13px; height:13px;">
                                                <path d="M3 7V5c0-1.1.9-2 2-2h4l2 2h8c1.1 0 2 .9 2 2v2H3zm0 12h18V9H3v10z"/>
                                            </svg>
                                            <span>{{ $notebook->category->name ?? 'Projects' }}</span>
                                        </div>
                                        <div class="notebook-owner">
                                            <div class="owner-avatar">
                                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <span>You</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <div x-show="viewMode === 'list'" class="notebooks-list" style="display: none;">
                        @foreach ($pinnedNotebooks as $notebook)
                            <div class="list-item">
                                <a href="{{ route('notebooks.show', $notebook) }}" class="list-cover" style="background: {{ $notebook->cover_color ?? '#1d4ed8' }}; text-decoration: none; color: inherit;">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </a>
                                <div class="list-content">
                                    <a href="{{ route('notebooks.show', $notebook) }}" style="text-decoration: none; color: inherit;">
                                        <div class="list-title">{{ $notebook->title }}</div>
                                    </a>
                                    <div class="list-meta">
                                        <span>{{ $notebook->category->name ?? 'Projects' }}</span>
                                        <span>Created {{ \Carbon\Carbon::parse($notebook->created_at)->format('j M Y') }}</span>
                                        <span>Owner: You</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Shelves (shown when filter is 'all' or 'my') -->
        <div x-show="showShelves" style="display: none;" x-transition>
            <div class="section">
                <div class="section-header">
                    <div class="section-title-area">
                        <div class="section-icon section-icon-shelves" style="background: linear-gradient(135deg, #10b981, #059669);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="section-greeting">Organize</div>
                            <h2 class="section-title">Shelves</h2>
                        </div>
                    </div>
                </div>

                <div class="notebooks-grid">
                    <button @click="showCreateShelfModal = true" class="btn-add-shelf" style="order: -1;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create Shelf
                    </button>

                    @foreach ($shelves as $shelf)
                        <div class="shelf-card">
                            <div class="shelf-header" @click="toggleShelf({{ $shelf->id }})">
                                <div class="shelf-header-left">
                                    <div class="shelf-icon" style="background: rgba(29, 78, 216, 0.1); color: #1d4ed8;">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="shelf-title">{{ $shelf->name }}</div>
                                        <div class="shelf-count">{{ $shelf->notebooks->count() }} notebooks</div>
                                    </div>
                                </div>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px; height:18px; color:#94a3b8; transition: transform 0.2s ease;" :style="{ transform: expandedShelves[{{ $shelf->id }}] ? 'rotate(180deg)' : 'rotate(0deg)' }">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                            <div x-show="expandedShelves[{{ $shelf->id }}]" x-collapse style="padding:16px;">
                                @if ($shelf->notebooks->isEmpty())
                                    <div class="empty-state" style="padding:20px 16px;">
                                        <div class="empty-state-title" style="font-size:14px;">No notebooks in this shelf</div>
                                        <div class="empty-state-text" style="font-size:12px;">Add notebooks to organize them</div>
                                    </div>
                                @else
                                    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(200px,1fr)); gap:10px;">
                                        @foreach ($shelf->notebooks as $notebook)
                                            <a href="{{ route('notebooks.show', $notebook) }}" class="shelf-notebook" style="text-decoration: none;">
                                                <div class="shelf-notebook-cover" style="background: linear-gradient(135deg, #1d4ed8 0%, #6d5ef9 100%);">
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                </div>
                                                <div class="shelf-notebook-title">{{ $notebook->title }}</div>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Shared Notebooks -->
        <div class="section">
            <div class="section-header">
                <div class="section-title-area">
                    <div class="section-icon section-icon-shared" style="background: linear-gradient(135deg, #06b6d4, #0891b2);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="section-greeting">Collaboration</div>
                        <h2 class="section-title">Shared Notebooks</h2>
                    </div>
                </div>
            </div>
            <div class="notebooks-grid">
                @if ($sharedNotebooks->isEmpty())
                    <div class="notebook-card create" style="height: 240px;">
                        <button type="button" disabled style="all: unset; width: 100%; height: 100%; cursor: default; display: flex; flex-direction: column; align-items: center; opacity: 0.6;">
                            <div class="create-icon-wrapper">
                                <svg class="create-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="create-text">No shared notebooks yet</div>
                            <div class="create-subtext">Wait for someone to share with you</div>
                        </button>
                    </div>
                @else
                    @foreach ($sharedNotebooks as $notebook)
                        <div class="notebook-card">
                            <div class="notebook-header">
                                <a href="{{ route('notebooks.show', $notebook) }}" class="notebook-cover" style="background: {{ $notebook->cover_color ?? '#1d4ed8' }}; text-decoration: none; color: inherit;">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </a>
                            </div>
                            <a href="{{ route('notebooks.show', $notebook) }}" style="text-decoration: none; color: inherit;">
                                <div class="notebook-title">{{ $notebook->title }}</div>
                            </a>
                            <div class="notebook-meta">
                                <div class="notebook-owner">
                                    <div class="owner-avatar">
                                        {{ strtoupper(substr($notebook->owner->name ?? 'N', 0, 1)) }}
                                    </div>
                                    <span>{{ $notebook->owner->name ?? 'User' }}</span>
                                </div>
                                <div class="shared-role">{{ $notebook->pivot->permission ?? 'Viewer' }}</div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div x-show="showCreateShelfModal" class="modal-overlay fixed inset-0 z-200 flex items-center justify-center" x-transition>
            <div class="modal-shell" style="max-width:480px; width:90%;">
                <h3>Create Shelf</h3>
                <form method="POST" action="{{ route('shelves.store') }}">
                    @csrf
                    <div style="margin-bottom:20px;">
                        <label>Shelf Name</label>
                        <input type="text" name="name" x-model="newShelfName" required>
                    </div>
                    <div style="margin-bottom:24px;">
                        <label>Icon</label>
                        <div style="display:flex; gap:8px; flex-wrap:wrap;">
                            <template x-for="icon in ['📚', '📁', '📂', '📊', '📈', '💼', '🎯', '🚀']">
                                <button type="button" @click="newShelfIcon = icon" :style="{ background: newShelfIcon === icon ? 'rgba(29,78,216,0.1)' : 'transparent', border: newShelfIcon === icon ? '2px solid #1d4ed8' : '2px solid transparent' }" style="width:40px; height:40px; border-radius:10px; font-size:18px; cursor:pointer; transition:all 0.2s ease;">
                                    <span x-text="icon"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                    <div class="modal-action-row">
                        <button type="button" @click="showCreateShelfModal = false; newShelfName = '';" class="modal-button modal-button-secondary">Cancel</button>
                        <button type="submit" class="modal-button modal-button-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="showRenameModal" class="modal-overlay fixed inset-0 z-200 flex items-center justify-center" x-transition>
            <div class="modal-shell" style="max-width:480px; width:90%;">
                <h3>Rename Notebook</h3>
                <form method="POST" :action="`/notebooks/${renameNotebookId}`">
                    @csrf
                    @method('PATCH')
                    <div style="margin-bottom:24px;">
                        <label>Notebook Name</label>
                        <input type="text" name="title" x-model="renameTitle" required>
                    </div>
                    <div class="modal-action-row">
                        <button type="button" @click="showRenameModal = false" class="modal-button modal-button-secondary">Cancel</button>
                        <button type="submit" class="modal-button modal-button-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
