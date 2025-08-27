import { createLazyFileRoute } from '@tanstack/react-router';

export const Route = createLazyFileRoute('/')({
    component: Index,
});

function Index() {
    return (
        <div>
            <h3>Home</h3>
        </div>
    );
}