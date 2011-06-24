package UI.graph;

import org.jgraph.JGraph;
import org.jgraph.graph.GraphLayoutCache;
import org.jgraph.graph.GraphModel;

public class Graph extends JGraph {

    public Graph(GraphModel model) {
        this(model, null);
    }

    public Graph(GraphModel model, GraphLayoutCache cache) {
        super(model, cache);
        // Make Ports Visible by Default
        setPortsVisible(true);
        // Use the Grid (but don't make it Visible)
        setGridEnabled(true);
        // Set the Grid Size to 10 Pixel
        setGridSize(6);
        // Set the Tolerance to 2 Pixel
        setTolerance(2);
        // Accept edits if click on background
        setInvokesStopCellEditing(true);
        // Jump to default port on connect
        setJumpToDefaultPort(true);
    }
}
