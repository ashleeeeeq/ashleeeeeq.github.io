<script>
    document.addEventListener('DOMContentLoaded', () => {
        const guardiansContainer = document.getElementById('guardians-container');
        const addButton = document.getElementById('add-guardian');
        const template = document.getElementById('guardian-template');

        if (!guardiansContainer || !addButton || !template) {
            return;
        }

        const nextIndex = () => parseInt(guardiansContainer.dataset.nextIndex || '0', 10);

        const updateNextIndex = (value) => {
            guardiansContainer.dataset.nextIndex = String(value);
        };

        addButton.addEventListener('click', () => {
            const index = nextIndex();
            const html = template.innerHTML
                .replace(/__INDEX__/g, index)
                .replace(/__NUMBER__/g, index + 1);

            const wrapper = document.createElement('div');
            wrapper.innerHTML = html.trim();
            const newBlock = wrapper.firstElementChild;
            guardiansContainer.appendChild(newBlock);
            updateNextIndex(index + 1);
            const csWrapper = newBlock.querySelector('.civil-status-wrapper');
            if (csWrapper && typeof window.initSingleCivilStatus === 'function') {
                window.initSingleCivilStatus(csWrapper);
            }
            if (typeof window.initializeChoices === 'function') {
                window.initializeChoices();
            }
        });

        guardiansContainer.addEventListener('click', (event) => {
            const btn = event.target.closest('.remove-guardian-btn');
            if (btn) {
                const block = btn.closest('.guardian-block');
                if (block) {
                    block.remove();
                }
            }
        });
    });
</script>
