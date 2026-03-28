<script>
    (function () {
        function initKanban() {
            const statuses = @js($statuses->pluck('id')->values()->toArray());

            statuses.forEach(function (status) {
                const el = document.querySelector(`[data-status-id='${status}']`);
                if (! el || el.__sortable) return;

                const instance = Sortable.create(el, {
                    group: 'pcm-filament-kanban',
                    ghostClass: 'opacity-40',
                    animation: 150,
                    forceFallback: false,

                    onStart() {
                        setTimeout(() => document.body.classList.add('grabbing'));
                    },

                    onEnd() {
                        document.body.classList.remove('grabbing');
                    },

                    setData(dataTransfer, el) {
                        dataTransfer.setData('id', el.id);
                    },

                    onAdd(e) {
                        const recordId      = e.item.id;
                        const status        = e.to.dataset.statusId;
                        const fromOrderedIds = [].slice.call(e.from.children).map(c => c.id);
                        const toOrderedIds   = [].slice.call(e.to.children).map(c => c.id);
                        Livewire.dispatch('status-changed', { recordId, status, fromOrderedIds, toOrderedIds });
                    },

                    onUpdate(e) {
                        const recordId   = e.item.id;
                        const status     = e.from.dataset.statusId;
                        const orderedIds = [].slice.call(e.from.children).map(c => c.id);
                        Livewire.dispatch('sort-changed', { recordId, status, orderedIds });
                    },
                });

                el.__sortable = instance;
            });
        }

        // Init on first load and after every Livewire navigation / full-page update
        document.addEventListener('livewire:navigated', initKanban);
        document.addEventListener('livewire:initialized', initKanban);

        // Re-init after Livewire DOM morph (handles partial re-renders)
        document.addEventListener('livewire:update', function () {
            // Destroy old instances so they get recreated cleanly
            const statuses = @js($statuses->pluck('id')->values()->toArray());
            statuses.forEach(function (status) {
                const el = document.querySelector(`[data-status-id='${status}']`);
                if (el && el.__sortable) {
                    el.__sortable.destroy();
                    delete el.__sortable;
                }
            });
            setTimeout(initKanban, 50);
        });
    })();
</script>