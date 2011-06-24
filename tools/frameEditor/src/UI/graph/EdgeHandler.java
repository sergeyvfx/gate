package UI.graph;

import java.awt.event.MouseEvent;
import org.jgraph.graph.EdgeView;
import org.jgraph.graph.GraphContext;

public class EdgeHandler extends EdgeView.EdgeHandle {

    public EdgeHandler(EdgeView edge, GraphContext ctx) {
        super(edge, ctx);
    }

    @Override
    public boolean isAddPointEvent(MouseEvent e) {
        // Points are added using Shift-Click
        return e.isShiftDown();
    }

    @Override
    public boolean isRemovePointEvent(MouseEvent e) {
        // Points are removed using Shift-Click
        return e.isShiftDown();
    }
}
